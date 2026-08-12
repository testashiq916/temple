<?php

namespace App\Http\Controllers\API\V1\Seva;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Seva\PrasadBooking;
use App\Services\Accounting\LedgerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PrasadController extends CompanyScopedController
{
    public function __construct(private readonly LedgerService $ledger)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $bookings = $this->paginate(
            PrasadBooking::where('company_id', $this->companyId($request))
                ->when($request->query('devotee_id'), fn ($q, $v) => $q->where('devotee_id', $v))
                ->with('devotee')
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
            'prasad_type' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'collection_date' => ['nullable', 'date'],
            'collection_time' => ['nullable', 'date_format:H:i'],
        ]);

        $data['company_id'] = $this->companyId($request);
        $data['booking_id'] = 'PRA-'.Str::upper(Str::random(8));
        $data['booking_date'] = now()->toDateString();
        $data['total_amount'] = $data['quantity'] * $data['unit_price'];

        $booking = PrasadBooking::create($data);

        return response()->json($booking, 201);
    }

    public function show(Request $request, PrasadBooking $prasadBooking): JsonResponse
    {
        abort_unless($prasadBooking->company_id === $this->companyId($request), 403);

        return response()->json($prasadBooking);
    }

    public function update(Request $request, PrasadBooking $prasadBooking): JsonResponse
    {
        abort_unless($prasadBooking->company_id === $this->companyId($request), 403);

        $data = $request->validate([
            'status' => ['sometimes', 'in:pending,confirmed,collected,cancelled'],
            'payment_status' => ['sometimes', 'in:pending,paid,refunded'],
        ]);

        $prasadBooking->update($data);

        return response()->json($prasadBooking);
    }

    public function confirmPayment(Request $request, PrasadBooking $prasadBooking): JsonResponse
    {
        abort_unless($prasadBooking->company_id === $this->companyId($request), 403);
        abort_if($prasadBooking->payment_status === 'paid', 422, 'Booking already paid');

        $this->ledger->recordPrasadSale($prasadBooking, $request->user()->id);
        $prasadBooking->update(['payment_status' => 'paid', 'status' => 'confirmed']);

        return response()->json($prasadBooking);
    }
}
