<?php

namespace App\Http\Controllers\API\V1\Financial;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Accounting\AccountM;
use App\Models\Accounting\Daybook;
use App\Models\Donation\Donation;
use App\Models\Seva\SevaBooking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends CompanyScopedController
{
    /**
     * Trial balance: current balance of every account, grouped by dr/cr natural side.
     */
    public function trialBalance(Request $request): JsonResponse
    {
        $accounts = AccountM::where('company_id', $this->companyId($request))
            ->where('status', 'active')
            ->orderBy('accode')
            ->get()
            ->map(fn (AccountM $account) => [
                'accode' => $account->accode,
                'name' => $account->name,
                'actype' => $account->actype,
                'balance' => $account->current_balance,
            ]);

        return response()->json([
            'accounts' => $accounts,
            'total_debit' => $accounts->where('actype', 'debit')->sum('balance'),
            'total_credit' => $accounts->where('actype', 'credit')->sum('balance'),
        ]);
    }

    public function incomeExpenseSummary(Request $request): JsonResponse
    {
        $companyId = $this->companyId($request);
        $from = $request->query('from', now()->startOfMonth()->toDateString());
        $to = $request->query('to', now()->toDateString());

        $income = AccountM::where('company_id', $companyId)->where('bshead', 'INCOME')
            ->get()
            ->map(fn (AccountM $a) => ['accode' => $a->accode, 'name' => $a->name, 'amount' => $a->current_balance]);

        $expenses = AccountM::where('company_id', $companyId)->where('bshead', 'EXPENSES')
            ->get()
            ->map(fn (AccountM $a) => ['accode' => $a->accode, 'name' => $a->name, 'amount' => $a->current_balance]);

        return response()->json([
            'period' => ['from' => $from, 'to' => $to],
            'income' => $income,
            'total_income' => $income->sum('amount'),
            'expenses' => $expenses,
            'total_expenses' => $expenses->sum('amount'),
            'net_surplus' => $income->sum('amount') - $expenses->sum('amount'),
        ]);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $companyId = $this->companyId($request);
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();

        return response()->json([
            'donations_today' => Donation::where('company_id', $companyId)->whereDate('donation_date', $today)->sum('amount'),
            'donations_this_month' => Donation::where('company_id', $companyId)->whereDate('donation_date', '>=', $monthStart)->sum('amount'),
            'seva_bookings_today' => SevaBooking::where('company_id', $companyId)->whereDate('booking_date', $today)->count(),
            'seva_revenue_this_month' => SevaBooking::where('company_id', $companyId)
                ->whereDate('booking_date', '>=', $monthStart)
                ->where('payment_status', 'paid')
                ->sum('net_amount'),
            'pending_seva_bookings' => SevaBooking::where('company_id', $companyId)->where('status', 'pending')->count(),
            'ledger_entries_today' => Daybook::where('company_id', $companyId)->whereDate('voucher_date', $today)->count(),
        ]);
    }
}
