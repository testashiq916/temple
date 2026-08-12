<?php

use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\Crowd\AnalyticsController;
use App\Http\Controllers\API\V1\Crowd\CameraController;
use App\Http\Controllers\API\V1\Crowd\QueueController;
use App\Http\Controllers\API\V1\Devotee\DevoteeController;
use App\Http\Controllers\API\V1\Devotee\DevoteeTypeController;
use App\Http\Controllers\API\V1\Devotee\DocumentController;
use App\Http\Controllers\API\V1\Donation\DigitalGoldController;
use App\Http\Controllers\API\V1\Donation\DonationCategoryController;
use App\Http\Controllers\API\V1\Donation\DonationController;
use App\Http\Controllers\API\V1\Donation\DonorController;
use App\Http\Controllers\API\V1\Donation\EHundiController;
use App\Http\Controllers\API\V1\Donation\ReceiptController;
use App\Http\Controllers\API\V1\Financial\AccountingController;
use App\Http\Controllers\API\V1\Financial\ReportController;
use App\Http\Controllers\API\V1\Financial\VoucherController;
use App\Http\Controllers\API\V1\Inventory\InventoryCategoryController;
use App\Http\Controllers\API\V1\Inventory\InventoryController;
use App\Http\Controllers\API\V1\Inventory\StockMovementController;
use App\Http\Controllers\API\V1\Property\LandRecordController;
use App\Http\Controllers\API\V1\Property\PropertyController;
use App\Http\Controllers\API\V1\Property\TenantController;
use App\Http\Controllers\API\V1\Seva\BookingController;
use App\Http\Controllers\API\V1\Seva\PrasadController;
use App\Http\Controllers\API\V1\Seva\SevaCategoryController;
use App\Http\Controllers\API\V1\Seva\SevaController;
use App\Http\Controllers\API\V1\Seva\SlotController;
use App\Http\Controllers\API\V1\Staff\StaffController;
use App\Http\Controllers\API\V1\Staff\StaffPositionController;
use App\Http\Controllers\API\V1\Staff\VolunteerController;
use App\Http\Controllers\API\V1\Temple\DeityController;
use App\Http\Controllers\API\V1\Temple\FestivalController;
use App\Http\Controllers\API\V1\Temple\TempleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // --- Public auth endpoints ---
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // --- Authenticated endpoints (tenant-scoped by the logged-in user's company_id) ---
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // Temple
        Route::apiResource('temples', TempleController::class);
        Route::apiResource('deities', DeityController::class)->except(['index', 'store']);
        Route::get('/deities', [DeityController::class, 'index']);
        Route::post('/deities', [DeityController::class, 'store']);
        Route::apiResource('festivals', FestivalController::class)->except(['index', 'store']);
        Route::get('/festivals', [FestivalController::class, 'index']);
        Route::post('/festivals', [FestivalController::class, 'store']);

        // Devotee
        Route::apiResource('devotees', DevoteeController::class);
        Route::apiResource('devotee-types', DevoteeTypeController::class)->only(['index', 'store']);
        Route::get('/devotees/{devotee}/documents', [DocumentController::class, 'index']);
        Route::post('/devotees/{devotee}/documents', [DocumentController::class, 'store']);
        Route::delete('/devotees/{devotee}/documents/{document}', [DocumentController::class, 'destroy']);

        // Seva
        Route::apiResource('seva-categories', SevaCategoryController::class)
            ->only(['index', 'store', 'update'])
            ->parameters(['seva-categories' => 'sevaCategory']);
        Route::apiResource('sevas', SevaController::class);
        Route::get('/sevas/{seva}/slots', [SlotController::class, 'index']);
        Route::post('/sevas/{seva}/slots', [SlotController::class, 'store']);
        Route::delete('/sevas/{seva}/slots/{slot}', [SlotController::class, 'destroy']);
        Route::apiResource('seva-bookings', BookingController::class)
            ->only(['index', 'store', 'show'])
            ->parameters(['seva-bookings' => 'booking']);
        Route::post('/seva-bookings/{booking}/confirm-payment', [BookingController::class, 'confirmPayment']);
        Route::post('/seva-bookings/{booking}/complete', [BookingController::class, 'complete']);
        Route::post('/seva-bookings/{booking}/cancel', [BookingController::class, 'cancel']);
        Route::apiResource('prasad-bookings', PrasadController::class)
            ->only(['index', 'store', 'show', 'update'])
            ->parameters(['prasad-bookings' => 'prasadBooking']);
        Route::post('/prasad-bookings/{prasadBooking}/confirm-payment', [PrasadController::class, 'confirmPayment']);

        // Donation
        Route::apiResource('donation-categories', DonationCategoryController::class)->only(['index', 'store']);
        Route::apiResource('donors', DonorController::class)->only(['index', 'store', 'show', 'update']);
        Route::apiResource('donations', DonationController::class)->only(['index', 'store', 'show']);
        Route::post('/donations/{donation}/verify', [DonationController::class, 'verify']);
        Route::apiResource('e-hundi', EHundiController::class)
            ->only(['index', 'store', 'show'])
            ->parameters(['e-hundi' => 'eHundiTransaction']);
        Route::apiResource('digital-gold', DigitalGoldController::class)
            ->only(['index', 'store', 'show'])
            ->parameters(['digital-gold' => 'digitalGoldTransaction']);
        Route::post('/digital-gold/{digitalGoldTransaction}/redeem', [DigitalGoldController::class, 'redeem']);
        Route::apiResource('receipts', ReceiptController::class)->only(['index', 'show']);

        // Financial / Accounting
        Route::get('/accounting/balance-sheet-heads', [AccountingController::class, 'balanceSheetHeads']);
        Route::get('/accounting/groups', [AccountingController::class, 'groups']);
        Route::get('/accounting/chart-of-accounts', [AccountingController::class, 'chartOfAccounts']);
        Route::post('/accounting/chart-of-accounts', [AccountingController::class, 'storeAccount']);
        Route::get('/accounting/ledger/{accode}', [AccountingController::class, 'accountLedger']);
        Route::apiResource('vouchers', VoucherController::class)->only(['index', 'store', 'show']);
        Route::get('/reports/trial-balance', [ReportController::class, 'trialBalance']);
        Route::get('/reports/income-expense', [ReportController::class, 'incomeExpenseSummary']);
        Route::get('/reports/dashboard', [ReportController::class, 'dashboard']);

        // Crowd
        Route::apiResource('queues', QueueController::class)->only(['index', 'store', 'show']);
        Route::post('/queues/{queue}/entries', [QueueController::class, 'addEntry']);
        Route::post('/queues/{queue}/entries/{entry}/complete', [QueueController::class, 'completeEntry']);
        Route::post('/queues/{queue}/close', [QueueController::class, 'close']);
        Route::get('/crowd-analytics', [AnalyticsController::class, 'index']);
        Route::get('/crowd-analytics/heatmap', [AnalyticsController::class, 'heatmap']);
        Route::apiResource('cameras', CameraController::class)->only(['index', 'store']);
        Route::get('/cameras/{camera}/alerts', [CameraController::class, 'alerts']);
        Route::post('/alerts/{alert}/resolve', [CameraController::class, 'resolveAlert']);

        // Property
        Route::apiResource('properties', PropertyController::class)->only(['index', 'store', 'show', 'update']);
        Route::get('/properties/{property}/land-records', [LandRecordController::class, 'index']);
        Route::post('/properties/{property}/land-records', [LandRecordController::class, 'store']);
        Route::get('/properties/{property}/tenants', [TenantController::class, 'index']);
        Route::post('/properties/{property}/tenants', [TenantController::class, 'store']);
        Route::patch('/properties/{property}/tenants/{tenant}', [TenantController::class, 'update']);

        // Inventory
        Route::apiResource('inventory-categories', InventoryCategoryController::class)->only(['index', 'store']);
        Route::apiResource('inventory-items', InventoryController::class)
            ->only(['index', 'store', 'show', 'update'])
            ->parameters(['inventory-items' => 'item']);
        Route::get('/inventory-items/{item}/movements', [StockMovementController::class, 'index']);
        Route::post('/inventory-items/{item}/movements', [StockMovementController::class, 'store']);

        // Staff
        Route::apiResource('staff-positions', StaffPositionController::class)->only(['index', 'store']);
        Route::apiResource('staff', StaffController::class)
            ->only(['index', 'store', 'show', 'update'])
            ->parameters(['staff' => 'staffMember']);
        Route::apiResource('volunteers', VolunteerController::class)->only(['index', 'store', 'update']);
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
