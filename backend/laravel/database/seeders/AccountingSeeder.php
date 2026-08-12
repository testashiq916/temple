<?php

namespace Database\Seeders;

use App\Models\Accounting\AccountG;
use App\Models\Accounting\AccountGBS;
use App\Models\Accounting\AccountM;
use App\Models\System\Company;
use Illuminate\Database\Seeder;

/**
 * Seeds the default chart of accounts for a temple, matching the reference
 * design notes: 4 balance-sheet heads, 8 account groups, and the standard
 * account codes referenced by LedgerService (101-108, 201-205, 301-306,
 * 401-407).
 */
class AccountingSeeder extends Seeder
{
    public function run(): void
    {
        Company::all()->each(function (Company $company) {
            $heads = [
                'ASSETS' => 'Assets',
                'LIABILITIES' => 'Liabilities',
                'INCOME' => 'Income',
                'EXPENSES' => 'Expenses',
            ];

            $headModels = [];
            foreach ($heads as $code => $name) {
                $headModels[$code] = AccountGBS::firstOrCreate(
                    ['company_id' => $company->id, 'bshead' => $code],
                    ['bshead_name' => $name]
                );
            }

            $groups = [
                'CA' => ['Current Assets', 'ASSETS'],
                'FA' => ['Fixed Assets', 'ASSETS'],
                'CL' => ['Current Liabilities', 'LIABILITIES'],
                'LL' => ['Long Term Liabilities', 'LIABILITIES'],
                'OP' => ['Operating Income', 'INCOME'],
                'OI' => ['Other Income', 'INCOME'],
                'OE' => ['Operating Expenses', 'EXPENSES'],
                'NE' => ['Non-Operating Expenses', 'EXPENSES'],
            ];

            foreach ($groups as $code => [$name, $headCode]) {
                AccountG::firstOrCreate(
                    ['company_id' => $company->id, 'grcode' => $code],
                    ['grname' => $name, 'bshead_id' => $headModels[$headCode]->id]
                );
            }

            $accounts = [
                // Assets
                ['101', 'Cash in Hand', 'CA', 'ASSETS', 'debit'],
                ['102', 'Bank Account - Main', 'CA', 'ASSETS', 'debit'],
                ['103', 'Bank Account - Digital Gold', 'CA', 'ASSETS', 'debit'],
                ['104', 'Accounts Receivable', 'CA', 'ASSETS', 'debit'],
                ['105', 'Security Deposit Receivable', 'CA', 'ASSETS', 'debit'],
                ['106', 'Temple Property', 'FA', 'ASSETS', 'debit'],
                ['107', 'Temple Gold Reserve', 'FA', 'ASSETS', 'debit'],
                ['108', 'Temple Silver Reserve', 'FA', 'ASSETS', 'debit'],
                // Liabilities
                ['201', 'Accounts Payable', 'CL', 'LIABILITIES', 'credit'],
                ['202', 'Security Deposit Payable', 'CL', 'LIABILITIES', 'credit'],
                ['203', 'GST Payable - CGST', 'CL', 'LIABILITIES', 'credit'],
                ['204', 'GST Payable - SGST', 'CL', 'LIABILITIES', 'credit'],
                ['205', 'Advance Seva Bookings', 'CL', 'LIABILITIES', 'credit'],
                // Income
                ['301', 'Donation Revenue', 'OP', 'INCOME', 'credit'],
                ['302', 'Seva Revenue', 'OP', 'INCOME', 'credit'],
                ['303', 'Prasad Sales', 'OP', 'INCOME', 'credit'],
                ['304', 'Rental Income', 'OP', 'INCOME', 'credit'],
                ['305', 'Interest Income', 'OI', 'INCOME', 'credit'],
                ['306', 'Digital Gold Income', 'OI', 'INCOME', 'credit'],
                // Expenses
                ['401', 'Staff Salaries', 'OE', 'EXPENSES', 'debit'],
                ['402', 'Utilities - Electricity', 'OE', 'EXPENSES', 'debit'],
                ['403', 'Utilities - Water', 'OE', 'EXPENSES', 'debit'],
                ['404', 'Temple Maintenance', 'OE', 'EXPENSES', 'debit'],
                ['405', 'Prasad Materials', 'OE', 'EXPENSES', 'debit'],
                ['406', 'Insurance', 'OE', 'EXPENSES', 'debit'],
                ['407', 'Bank Charges', 'NE', 'EXPENSES', 'debit'],
            ];

            foreach ($accounts as [$accode, $name, $grcode, $bshead, $actype]) {
                AccountM::firstOrCreate(
                    ['company_id' => $company->id, 'accode' => $accode],
                    [
                        'name' => $name,
                        'grcode' => $grcode,
                        'bshead' => $bshead,
                        'actype' => $actype,
                        'opening_balance' => 0,
                        'is_default' => true,
                    ]
                );
            }
        });
    }
}
