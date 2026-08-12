<?php

namespace App\Http\Controllers\API\V1\Seva;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Seva\SevaBooking;
use App\Models\Seva\SevaSlot;
use App\Services\Accounting\LedgerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends CompanyScopedController
{
    public function __construct(private readonly LedgerService $ledger)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $bookings = $this->paginate(
            SevaBooking::where('company_id', $this->companyId($request))
                ->when($request->query('devotee_id'), fn ($q, $v) => $q->where('devotee_id', $v))
                ->when($request->query('status'), fn ($q, $v) => $q->where('status', $v))
                ->with(['devotee', 'seva', 'slot'])
                ->latest(),
            $request
        );

        return response()->json($bookings);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'temple_id' => ['required', 'exists:temples,id'],
            'devotee_id' => ['required', 'exists:devotees,id'],
            'seva_id' => ['required', 'exists:seva_services,id'],
            'slot_id' => ['required', 'exists:seva_slots,id'],
            'number_of_devotees' => ['nullable', 'integer', 'min:1'],
            'special_requests' => ['nullable', 'string'],
        ]);

        $booking = DB::transaction(function () use ($data, $request) {
            $slot = SevaSlot::lockForUpdate()->findOrFail($data['slot_id']);
            abort_if($slot->booked_count >= $slot->capacity, 422, 'This slot is fully booked');

            $seva = $slot->seva;
            $numberOfDevotees = $data['number_of_devotees'] ?? 1;
            $totalAmount = (float) $seva->price * $numberOfDevotees;
            $taxAmount = round($totalAmount * ((float) $seva->gst_rate / 100), 2);

            $booking = SevaBooking::create([
                'company_id' => $this->companyId($request),
                'temple_id' => $data['temple_id'],
                'booking_id' => 'SEV-'.Str::upper(Str::random(8)),
                'devotee_id' => $data['devotee_id'],
                'seva_id' => $seva->id,
                'slot_id' => $slot->id,
                'booking_date' => now()->toDateString(),
                'slot_date' => $slot->slot_date,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'number_of_devotees' => $numberOfDevotees,
                'special_requests' => $data['special_requests'] ?? null,
                'total_amount' => $totalAmount,
                'tax_amount' => $taxAmount,
                'net_amount' => $totalAmount + $taxAmount,
                'status' => $seva->requires_approval ? 'pending' : 'confirmed',
                'created_by' => $request->user()->id,
            ]);

            $slot->increment('booked_count');

            return $booking;
        });

        return response()->json($booking->load('seva', 'slot'), 201);
    }

    public function show(Request $request, SevaBooking $booking): JsonResponse
    {
        abort_unless($booking->company_id === $this->companyId($request), 403);

        return response()->json($booking->load('devotee', 'seva', 'slot'));
    }

    public function confirmPayment(Request $request, SevaBooking $booking): JsonResponse
    {
        abort_unless($booking->company_id === $this->companyId($request), 403);
        abort_if($booking->payment_status === 'paid', 422, 'Booking already paid');

        $this->ledger->recordSevaPayment($booking, $request->user()->id);
        $booking->update(['payment_status' => 'paid', 'status' => 'confirmed']);

        return response()->json($booking);
    }

    public function complete(Request $request, SevaBooking $booking): JsonResponse
    {
        abort_unless($booking->company_id === $this->companyId($request), 403);
        abort_unless($booking->status === 'confirmed', 422, 'Only confirmed bookings can be completed');
        abort_unless($booking->payment_status === 'paid', 422, 'Booking payment must be settled before completion');

        $this->ledger->recordSevaCompletion($booking, $request->user()->id);
        $booking->update(['status' => 'completed']);

        return response()->json($booking);
    }

    public function cancel(Request $request, SevaBooking $booking): JsonResponse
    {
        abort_unless($booking->company_id === $this->companyId($request), 403);
        abort_if(in_array($booking->status, ['completed', 'cancelled'], true), 422, 'Booking cannot be cancelled');

        DB::transaction(function () use ($booking, $request) {
            $booking->slot()->decrement('booked_count');
            if ($booking->payment_status === 'paid') {
                $this->ledger->recordSevaCancellation($booking, $request->user()->id);
                $booking->payment_status = 'refunded';
            }
            $booking->status = 'cancelled';
            $booking->save();
        });

        return response()->json($booking);
    }
}
