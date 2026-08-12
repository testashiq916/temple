<?php

namespace Database\Seeders;

use App\Models\Devotee\Devotee;
use App\Models\Devotee\DevoteeType;
use App\Models\Donation\DonationCategory;
use App\Models\Donation\Donor;
use App\Models\Inventory\InventoryCategory;
use App\Models\Seva\SevaCategory;
use App\Models\Seva\SevaService;
use App\Models\Seva\SevaSlot;
use App\Models\Staff\StaffPosition;
use App\Models\System\Company;
use App\Models\System\Role;
use App\Models\System\User;
use App\Models\Temple\Deity;
use App\Models\Temple\Festival;
use App\Models\Temple\Temple;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TempleDemoSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['code' => 'SVT-001'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Sri Venkateswara Temple Trust',
                'email' => 'admin@srivenkateswara.example',
                'city' => 'Tirupati',
                'state' => 'Andhra Pradesh',
                'country' => 'India',
                'subscription_status' => 'active',
                'subscription_start_date' => now()->toDateString(),
                'subscription_end_date' => now()->addYear()->toDateString(),
            ]
        );

        $adminRole = Role::where('slug', 'admin')->first();

        $admin = User::firstOrCreate(
            ['email' => 'admin@temple.test'],
            [
                'company_id' => $company->id,
                'role_id' => $adminRole?->id,
                'name' => 'Temple Admin',
                'password' => Hash::make('password'),
            ]
        );

        $temple = Temple::firstOrCreate(
            ['temple_id' => 'TMPL-SVT001'],
            [
                'company_id' => $company->id,
                'name' => 'Sri Venkateswara Swamy Temple',
                'sanskrit_name' => 'श्री वेंकटेश्वर स्वामी मंदिर',
                'description' => 'A historic Vaishnavite temple dedicated to Lord Venkateswara.',
                'city' => 'Tirupati',
                'state' => 'Andhra Pradesh',
                'country' => 'India',
                'established_year' => 300,
                'deity_name' => 'Lord Venkateswara',
                'temple_type' => 'vaishno',
                'capacity' => 5000,
                'created_by' => $admin->id,
            ]
        );

        Deity::firstOrCreate(
            ['deity_id' => 'DEITY-VNK001'],
            [
                'company_id' => $company->id,
                'temple_id' => $temple->id,
                'name' => 'Lord Venkateswara',
                'sanskrit_name' => 'वेंकटेश्वर',
                'consort_name' => 'Padmavati',
                'is_primary' => true,
            ]
        );

        Festival::firstOrCreate(
            ['festival_id' => 'FEST-BRAH001'],
            [
                'company_id' => $company->id,
                'temple_id' => $temple->id,
                'name' => 'Brahmotsavam',
                'festival_type' => 'annual',
                'start_date' => now()->addMonths(2)->toDateString(),
                'end_date' => now()->addMonths(2)->addDays(9)->toDateString(),
                'is_recurring' => true,
            ]
        );

        $devoteeType = DevoteeType::firstOrCreate(
            ['company_id' => $company->id, 'temple_id' => $temple->id, 'name' => 'General'],
        );

        $devoteeUser = User::firstOrCreate(
            ['email' => 'devotee@temple.test'],
            [
                'company_id' => $company->id,
                'role_id' => Role::where('slug', 'devotee')->first()?->id,
                'name' => 'Ramesh Kumar',
                'password' => Hash::make('password'),
            ]
        );

        $devotee = Devotee::firstOrCreate(
            ['devotee_id' => 'DEV-DEMO0001'],
            [
                'company_id' => $company->id,
                'temple_id' => $temple->id,
                'user_id' => $devoteeUser->id,
                'devotee_type_id' => $devoteeType->id,
                'first_name' => 'Ramesh',
                'last_name' => 'Kumar',
                'gender' => 'male',
                'email' => 'devotee@temple.test',
                'mobile' => '9876543210',
                'city' => 'Tirupati',
                'state' => 'Andhra Pradesh',
                'country' => 'India',
                'is_member' => true,
                'created_by' => $admin->id,
            ]
        );

        Donor::firstOrCreate(
            ['donor_id' => 'DNR-DEMO0001'],
            [
                'company_id' => $company->id,
                'temple_id' => $temple->id,
                'devotee_id' => $devotee->id,
                'full_name' => 'Ramesh Kumar',
                'email' => 'devotee@temple.test',
                'mobile' => '9876543210',
                'donor_type' => 'individual',
            ]
        );

        DonationCategory::firstOrCreate(
            ['company_id' => $company->id, 'temple_id' => $temple->id, 'name' => 'General Donation'],
        );
        DonationCategory::firstOrCreate(
            ['company_id' => $company->id, 'temple_id' => $temple->id, 'name' => 'Annadanam (Food Offering)'],
        );

        $sevaCategory = SevaCategory::firstOrCreate(
            ['company_id' => $company->id, 'temple_id' => $temple->id, 'name' => 'Daily Sevas'],
        );

        $seva = SevaService::firstOrCreate(
            ['seva_id' => 'SEVA-DEMO0001'],
            [
                'company_id' => $company->id,
                'temple_id' => $temple->id,
                'category_id' => $sevaCategory->id,
                'name' => 'Archana',
                'sanskrit_name' => 'अर्चना',
                'description' => 'Ritual worship with recitation of the deity\'s names.',
                'duration_minutes' => 30,
                'price' => 500,
                'gst_rate' => 18,
                'max_devotees' => 5,
                'requires_approval' => false,
                'created_by' => $admin->id,
            ]
        );

        SevaSlot::firstOrCreate(
            ['seva_id' => $seva->id, 'slot_date' => now()->addDay()->toDateString(), 'start_time' => '07:00:00'],
            ['end_time' => '07:30:00', 'capacity' => 10]
        );
        SevaSlot::firstOrCreate(
            ['seva_id' => $seva->id, 'slot_date' => now()->addDay()->toDateString(), 'start_time' => '08:00:00'],
            ['end_time' => '08:30:00', 'capacity' => 10]
        );

        StaffPosition::firstOrCreate(
            ['company_id' => $company->id, 'temple_id' => $temple->id, 'name' => 'Priest'],
            ['salary_range_min' => 15000, 'salary_range_max' => 30000]
        );
        StaffPosition::firstOrCreate(
            ['company_id' => $company->id, 'temple_id' => $temple->id, 'name' => 'Security Guard'],
            ['salary_range_min' => 12000, 'salary_range_max' => 18000]
        );

        InventoryCategory::firstOrCreate(
            ['company_id' => $company->id, 'temple_id' => $temple->id, 'name' => 'Pooja Materials'],
        );
    }
}
