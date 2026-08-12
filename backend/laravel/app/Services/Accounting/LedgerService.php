<?php

namespace App\Services\Accounting;

use App\Models\Accounting\Daybook;
use App\Models\Accounting\Voucher;
use App\Models\Donation\Donation;
use App\Models\Seva\PrasadBooking;
use App\Models\Seva\SevaBooking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Posts double-entry ledger rows to daybook, mirroring the worked examples
 * in the original design notes (donation, seva booking/completion/refund,
 * prasad sale). Standard account codes come from the seeded chart of
 * accounts (see AccountingSeeder): 101 Cash, 102 Bank, 107 Digital Gold
 * Reserve, 205 Advance Seva Bookings, 301 Donation Revenue, 302 Seva
 * Revenue, 303 Prasad Sales.
 */
class LedgerService
{
    public function recordSevaPayment(SevaBooking $booking, int $userId): Voucher
    {
        return $this->post(
            companyId: $booking->company_id,
            voucherType: 'seva',
            voucherNoPrefix: 'SEV',
            date: now()->toDateString(),
            userId: $userId,
            narration: "Seva booking payment: {$booking->booking_id}",
            totalAmount: (float) $booking->net_amount,
            legs: [
                ['accode' => '102', 'opaccode' => '205', 'amount' => $booking->net_amount, 'drcr' => 'dr', 'remarks' => 'Seva booking payment received'],
                ['accode' => '205', 'opaccode' => '102', 'amount' => $booking->net_amount, 'drcr' => 'cr', 'remarks' => 'Advance seva liability'],
            ],
            sevaBookingId: $booking->id,
        );
    }

    public function recordSevaCompletion(SevaBooking $booking, int $userId): Voucher
    {
        return $this->post(
            companyId: $booking->company_id,
            voucherType: 'seva',
            voucherNoPrefix: 'SEV',
            date: now()->toDateString(),
            userId: $userId,
            narration: "Seva completed: {$booking->booking_id}",
            totalAmount: (float) $booking->net_amount,
            legs: [
                ['accode' => '205', 'opaccode' => '302', 'amount' => $booking->net_amount, 'drcr' => 'dr', 'remarks' => 'Seva completed'],
                ['accode' => '302', 'opaccode' => '205', 'amount' => $booking->net_amount, 'drcr' => 'cr', 'remarks' => 'Seva revenue'],
            ],
            sevaBookingId: $booking->id,
        );
    }

    public function recordSevaCancellation(SevaBooking $booking, int $userId): Voucher
    {
        return $this->post(
            companyId: $booking->company_id,
            voucherType: 'credit_note',
            voucherNoPrefix: 'CN',
            date: now()->toDateString(),
            userId: $userId,
            narration: "Seva cancellation refund: {$booking->booking_id}",
            totalAmount: (float) $booking->net_amount,
            legs: [
                ['accode' => '302', 'opaccode' => '205', 'amount' => $booking->net_amount, 'drcr' => 'dr', 'remarks' => 'Seva cancellation refund'],
                ['accode' => '205', 'opaccode' => '302', 'amount' => $booking->net_amount, 'drcr' => 'cr', 'remarks' => 'Refund liability created'],
            ],
            sevaBookingId: $booking->id,
        );
    }

    public function recordDonation(Donation $donation, int $userId): Voucher
    {
        $receivingAccode = $donation->donation_type === 'gold' ? '107' : '102';

        return $this->post(
            companyId: $donation->company_id,
            voucherType: 'receipt',
            voucherNoPrefix: 'RCP',
            date: $donation->donation_date->toDateString(),
            userId: $userId,
            narration: "Donation received: {$donation->donation_id}",
            totalAmount: (float) $donation->amount,
            legs: [
                ['accode' => $receivingAccode, 'opaccode' => '301', 'amount' => $donation->amount, 'drcr' => 'dr', 'remarks' => 'Donation received'],
                ['accode' => '301', 'opaccode' => $receivingAccode, 'amount' => $donation->amount, 'drcr' => 'cr', 'remarks' => 'Donation revenue'],
            ],
            donationId: $donation->id,
        );
    }

    public function recordPrasadSale(PrasadBooking $prasadBooking, int $userId): Voucher
    {
        return $this->post(
            companyId: $prasadBooking->company_id,
            voucherType: 'payment',
            voucherNoPrefix: 'PAY',
            date: now()->toDateString(),
            userId: $userId,
            narration: "Prasad booking payment: {$prasadBooking->booking_id}",
            totalAmount: (float) $prasadBooking->total_amount,
            legs: [
                ['accode' => '102', 'opaccode' => '303', 'amount' => $prasadBooking->total_amount, 'drcr' => 'dr', 'remarks' => 'Prasad booking payment'],
                ['accode' => '303', 'opaccode' => '102', 'amount' => $prasadBooking->total_amount, 'drcr' => 'cr', 'remarks' => 'Prasad revenue'],
            ],
        );
    }

    /**
     * @param  array<int, array{accode: string, opaccode: string, amount: float|string, drcr: string, remarks: string}>  $legs
     */
    private function post(
        int $companyId,
        string $voucherType,
        string $voucherNoPrefix,
        string $date,
        int $userId,
        string $narration,
        float $totalAmount,
        array $legs,
        ?int $sevaBookingId = null,
        ?int $donationId = null,
    ): Voucher {
        return DB::transaction(function () use ($companyId, $voucherType, $voucherNoPrefix, $date, $userId, $narration, $totalAmount, $legs, $sevaBookingId, $donationId) {
            $voucherNo = $voucherNoPrefix.'-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));

            $voucher = Voucher::create([
                'company_id' => $companyId,
                'voucher_no' => $voucherNo,
                'voucher_type' => $voucherType,
                'voucher_date' => $date,
                'narration' => $narration,
                'total_amount' => $totalAmount,
                'is_posted' => true,
                'posted_by' => $userId,
                'posted_at' => now(),
                'created_by' => $userId,
            ]);

            $nextSno = (int) Daybook::where('company_id', $companyId)->max('sno') + 1;

            foreach ($legs as $i => $leg) {
                Daybook::create([
                    'company_id' => $companyId,
                    'sno' => $nextSno + $i,
                    'accode' => $leg['accode'],
                    'opaccode' => $leg['opaccode'],
                    'amount' => $leg['amount'],
                    'drcr' => $leg['drcr'],
                    'voucher_type' => $voucherType,
                    'voucher_no' => $voucherNo,
                    'voucher_date' => $date,
                    'remarks' => $leg['remarks'],
                    'seva_booking_id' => $sevaBookingId,
                    'donation_id' => $donationId,
                    'created_by' => $userId,
                ]);
            }

            return $voucher;
        });
    }
}
