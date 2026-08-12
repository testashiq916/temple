<?php

namespace App\Http\Controllers\API\V1\Donation;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Donation\EHundiTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EHundiController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        $transactions = $this->paginate(
            EHundiTransaction::where('company_id', $this->companyId($request))->latest(),
            $request
        );

        return response()->json($transactions);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'temple_id' => ['required', 'exists:temples,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['nullable', 'in:upi,card,netbanking'],
            'upi_id' => ['nullable', 'string', 'max:100'],
        ]);

        $data['company_id'] = $this->companyId($request);
        $data['transaction_id'] = 'EHUNDI-'.Str::upper(Str::random(10));
        $data['transaction_date'] = now()->toDateString();
        $data['qr_code'] = 'QR-'.Str::upper(Str::random(12));
        $data['status'] = 'pending';

        $transaction = EHundiTransaction::create($data);

        return response()->json($transaction, 201);
    }

    public function show(Request $request, EHundiTransaction $eHundiTransaction): JsonResponse
    {
        abort_unless($eHundiTransaction->company_id === $this->companyId($request), 403);

        return response()->json($eHundiTransaction);
    }
}
