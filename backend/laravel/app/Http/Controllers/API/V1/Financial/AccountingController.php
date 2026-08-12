<?php

namespace App\Http\Controllers\API\V1\Financial;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Accounting\AccountG;
use App\Models\Accounting\AccountGBS;
use App\Models\Accounting\AccountM;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountingController extends CompanyScopedController
{
    public function balanceSheetHeads(Request $request): JsonResponse
    {
        return response()->json(
            AccountGBS::where('company_id', $this->companyId($request))->get()
        );
    }

    public function groups(Request $request): JsonResponse
    {
        return response()->json(
            AccountG::where('company_id', $this->companyId($request))->with('balanceSheetHead')->get()
        );
    }

    public function chartOfAccounts(Request $request): JsonResponse
    {
        return response()->json(
            AccountM::where('company_id', $this->companyId($request))
                ->when($request->query('bshead'), fn ($q, $v) => $q->where('bshead', $v))
                ->orderBy('accode')
                ->get()
        );
    }

    public function storeAccount(Request $request): JsonResponse
    {
        $data = $request->validate([
            'accode' => ['required', 'string', 'max:20', 'unique:accountm,accode'],
            'name' => ['required', 'string', 'max:255'],
            'grcode' => ['required', 'exists:accountg,grcode'],
            'bshead' => ['required', 'exists:accountgbs,bshead'],
            'actype' => ['required', 'in:debit,credit'],
            'opening_balance' => ['nullable', 'numeric'],
        ]);

        $data['company_id'] = $this->companyId($request);
        $data['created_by'] = $request->user()->id;

        $account = AccountM::create($data);

        return response()->json($account, 201);
    }

    public function accountLedger(Request $request, string $accode): JsonResponse
    {
        $account = AccountM::where('company_id', $this->companyId($request))
            ->where('accode', $accode)
            ->firstOrFail();

        $entries = $account->daybookEntries()
            ->when($request->query('from'), fn ($q, $v) => $q->whereDate('voucher_date', '>=', $v))
            ->when($request->query('to'), fn ($q, $v) => $q->whereDate('voucher_date', '<=', $v))
            ->orderBy('voucher_date')
            ->orderBy('sno')
            ->get();

        return response()->json([
            'account' => $account,
            'current_balance' => $account->current_balance,
            'entries' => $entries,
        ]);
    }
}
