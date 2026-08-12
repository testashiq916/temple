<?php

namespace App\Http\Controllers\API\V1\Donation;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Donation\Donation;
use App\Models\Donation\Receipt;
use App\Services\Accounting\LedgerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DonationController extends CompanyScopedController
{
    public function __construct(private readonly LedgerService $ledger)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $donations = $this->paginate(
            Donation::where('company_id', $this->companyId($request))
                ->when($request->query('donor_id'), fn ($q, $v) => $q->where('donor_id', $v))
                ->when($request->query('status'), fn ($q, $v) => $q->where('status', $v))
                ->with(['donor', 'category'])
                ->latest(),
            $request
        );

        return response()->json($donations);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'temple_id' => ['required', 'exists:temples,id'],
            'donor_id' => ['required', 'exists:donors,id'],
            'devotee_id' => ['nullable', 'exists:devotees,id'],
            'category_id' => ['nullable', 'exists:donation_categories,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'donation_type' => ['nullable', 'in:cash,digital,gold,silver,kind'],
            'payment_method' => ['nullable', 'in:cash,upi,card,netbanking,cheque,digital_gold,hundi'],
            'transaction_id' => ['nullable', 'string'],
            'is_anonymous' => ['nullable', 'boolean'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $donation = DB::transaction(function () use ($data, $request) {
            $data['company_id'] = $this->companyId($request);
            $data['donation_id'] = 'DON-'.Str::upper(Str::random(8));
            $data['donation_date'] = now()->toDateString();
            $data['status'] = 'received';
            $data['created_by'] = $request->user()->id;

            $donation = Donation::create($data);

            $receipt = Receipt::create([
                'company_id' => $donation->company_id,
                'temple_id' => $donation->temple_id,
                'receipt_no' => 'RCP-'.Str::upper(Str::random(8)),
                'devotee_id' => $donation->devotee_id,
                'donor_id' => $donation->donor_id,
                'receipt_date' => $donation->donation_date,
                'receipt_type' => 'donation',
                'amount' => $donation->amount,
                'payment_method' => $donation->payment_method,
                'transaction_id' => $donation->transaction_id,
                'created_by' => $request->user()->id,
            ]);

            $voucher = $this->ledger->recordDonation($donation, $request->user()->id);

            $donation->update(['receipt_id' => $receipt->id, 'voucher_id' => $voucher->id]);

            return $donation;
        });

        return response()->json($donation->load('donor', 'category'), 201);
    }

    public function show(Request $request, Donation $donation): JsonResponse
    {
        abort_unless($donation->company_id === $this->companyId($request), 403);

        return response()->json($donation->load('donor', 'category', 'receipt'));
    }

    public function verify(Request $request, Donation $donation): JsonResponse
    {
        abort_unless($donation->company_id === $this->companyId($request), 403);

        $donation->update([
            'status' => 'verified',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return response()->json($donation);
    }
}
