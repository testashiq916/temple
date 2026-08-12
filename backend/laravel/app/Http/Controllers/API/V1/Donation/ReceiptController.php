<?php

namespace App\Http\Controllers\API\V1\Donation;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Donation\Receipt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReceiptController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        $receipts = $this->paginate(
            Receipt::where('company_id', $this->companyId($request))
                ->when($request->query('devotee_id'), fn ($q, $v) => $q->where('devotee_id', $v))
                ->when($request->query('receipt_type'), fn ($q, $v) => $q->where('receipt_type', $v))
                ->latest(),
            $request
        );

        return response()->json($receipts);
    }

    public function show(Request $request, Receipt $receipt): JsonResponse
    {
        abort_unless($receipt->company_id === $this->companyId($request), 403);

        return response()->json($receipt->load('devotee', 'donor', 'voucher'));
    }
}
