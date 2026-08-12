<?php

namespace App\Http\Controllers\API\V1\Donation;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Donation\DigitalGoldTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DigitalGoldController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        $transactions = $this->paginate(
            DigitalGoldTransaction::where('company_id', $this->companyId($request))
                ->when($request->query('devotee_id'), fn ($q, $v) => $q->where('devotee_id', $v))
                ->latest(),
            $request
        );

        return response()->json($transactions);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'temple_id' => ['required', 'exists:temples,id'],
            'devotee_id' => ['nullable', 'exists:devotees,id'],
            'donor_id' => ['nullable', 'exists:donors,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'gold_weight_grams' => ['required', 'numeric', 'min:0.001'],
            'gold_purity' => ['nullable', 'in:24k,22k,18k'],
            'payment_method' => ['nullable', 'string', 'max:50'],
        ]);

        $data['company_id'] = $this->companyId($request);
        $data['transaction_id'] = 'DGOLD-'.Str::upper(Str::random(8));
        $data['transaction_date'] = now()->toDateString();
        $data['status'] = 'pending';

        $transaction = DigitalGoldTransaction::create($data);

        return response()->json($transaction, 201);
    }

    public function show(Request $request, DigitalGoldTransaction $digitalGoldTransaction): JsonResponse
    {
        abort_unless($digitalGoldTransaction->company_id === $this->companyId($request), 403);

        return response()->json($digitalGoldTransaction);
    }

    public function redeem(Request $request, DigitalGoldTransaction $digitalGoldTransaction): JsonResponse
    {
        abort_unless($digitalGoldTransaction->company_id === $this->companyId($request), 403);

        $data = $request->validate(['redeem_to' => ['required', 'string', 'max:255']]);

        $digitalGoldTransaction->update([
            'status' => 'redeemed',
            'redemption_date' => now()->toDateString(),
            'redeem_to' => $data['redeem_to'],
        ]);

        return response()->json($digitalGoldTransaction);
    }
}
