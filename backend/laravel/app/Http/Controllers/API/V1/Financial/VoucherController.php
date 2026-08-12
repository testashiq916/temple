<?php

namespace App\Http\Controllers\API\V1\Financial;

use App\Http\Controllers\API\V1\CompanyScopedController;
use App\Models\Accounting\Daybook;
use App\Models\Accounting\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VoucherController extends CompanyScopedController
{
    public function index(Request $request): JsonResponse
    {
        $vouchers = $this->paginate(
            Voucher::where('company_id', $this->companyId($request))
                ->when($request->query('voucher_type'), fn ($q, $v) => $q->where('voucher_type', $v))
                ->when($request->query('from'), fn ($q, $v) => $q->whereDate('voucher_date', '>=', $v))
                ->when($request->query('to'), fn ($q, $v) => $q->whereDate('voucher_date', '<=', $v))
                ->latest('voucher_date'),
            $request
        );

        return response()->json($vouchers);
    }

    /**
     * Manual journal voucher: caller supplies balanced debit/credit legs directly.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'voucher_type' => ['required', 'in:journal,contra,payment,receipt'],
            'voucher_date' => ['required', 'date'],
            'narration' => ['nullable', 'string'],
            'legs' => ['required', 'array', 'min:2'],
            'legs.*.accode' => ['required', 'exists:accountm,accode'],
            'legs.*.opaccode' => ['required', 'exists:accountm,accode'],
            'legs.*.amount' => ['required', 'numeric', 'min:0.01'],
            'legs.*.drcr' => ['required', 'in:dr,cr'],
            'legs.*.remarks' => ['nullable', 'string'],
        ]);

        $totalDr = collect($data['legs'])->where('drcr', 'dr')->sum('amount');
        $totalCr = collect($data['legs'])->where('drcr', 'cr')->sum('amount');
        abort_unless(round($totalDr, 2) === round($totalCr, 2), 422, 'Voucher is not balanced: total debits must equal total credits');

        $voucher = DB::transaction(function () use ($data, $request, $totalDr) {
            $companyId = $this->companyId($request);
            $voucherNo = strtoupper(Str::random(4)).'-'.now()->format('Ymd').'-'.Str::upper(Str::random(4));

            $voucher = Voucher::create([
                'company_id' => $companyId,
                'voucher_no' => $voucherNo,
                'voucher_type' => $data['voucher_type'],
                'voucher_date' => $data['voucher_date'],
                'narration' => $data['narration'] ?? null,
                'total_amount' => $totalDr,
                'is_posted' => true,
                'posted_by' => $request->user()->id,
                'posted_at' => now(),
                'created_by' => $request->user()->id,
            ]);

            $nextSno = (int) Daybook::where('company_id', $companyId)->max('sno') + 1;

            foreach ($data['legs'] as $i => $leg) {
                Daybook::create([
                    'company_id' => $companyId,
                    'sno' => $nextSno + $i,
                    'accode' => $leg['accode'],
                    'opaccode' => $leg['opaccode'],
                    'amount' => $leg['amount'],
                    'drcr' => $leg['drcr'],
                    'voucher_type' => $data['voucher_type'],
                    'voucher_no' => $voucherNo,
                    'voucher_date' => $data['voucher_date'],
                    'remarks' => $leg['remarks'] ?? null,
                    'created_by' => $request->user()->id,
                ]);
            }

            return $voucher;
        });

        return response()->json($voucher, 201);
    }

    public function show(Request $request, Voucher $voucher): JsonResponse
    {
        abort_unless($voucher->company_id === $this->companyId($request), 403);

        return response()->json($voucher->load('entries'));
    }
}
