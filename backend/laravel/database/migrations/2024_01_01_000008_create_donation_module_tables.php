<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->string('name');
            $table->string('sanskrit_name')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->string('donor_id', 50)->unique();
            $table->foreignId('devotee_id')->nullable()->constrained('devotees')->nullOnDelete();
            $table->string('full_name');
            $table->string('sanskrit_name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->enum('donor_type', ['individual', 'corporate', 'trust', 'nri'])->default('individual');
            $table->string('pan_card', 50)->nullable();
            $table->boolean('tax_exempt')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->string('receipt_no', 50)->unique();
            $table->foreignId('devotee_id')->nullable()->constrained('devotees')->nullOnDelete();
            $table->foreignId('donor_id')->nullable()->constrained('donors')->nullOnDelete();
            $table->date('receipt_date');
            $table->enum('receipt_type', ['donation', 'seva', 'prasad', 'membership', 'other']);
            $table->decimal('amount', 15, 2);
            $table->string('payment_method', 50)->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->foreignId('donor_id')->constrained('donors');
            $table->foreignId('devotee_id')->nullable()->constrained('devotees')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('donation_categories');
            $table->string('donation_id', 50)->unique();
            $table->decimal('amount', 15, 2);
            $table->date('donation_date');
            $table->enum('donation_type', ['cash', 'digital', 'gold', 'silver', 'kind'])->default('cash');
            $table->enum('payment_method', ['cash', 'upi', 'card', 'netbanking', 'cheque', 'digital_gold', 'hundi'])->default('cash');
            $table->string('transaction_id')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_recurring')->default(false);
            $table->json('recurrence_pattern')->nullable();
            $table->string('purpose')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'received', 'verified', 'refunded'])->default('pending');
            $table->foreignId('receipt_id')->nullable()->constrained('receipts')->nullOnDelete();
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('digital_gold_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->string('transaction_id', 50)->unique();
            $table->foreignId('devotee_id')->nullable()->constrained('devotees')->nullOnDelete();
            $table->foreignId('donor_id')->nullable()->constrained('donors')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->decimal('gold_weight_grams', 10, 3);
            $table->enum('gold_purity', ['24k', '22k', '18k'])->default('24k');
            $table->date('transaction_date');
            $table->string('payment_method', 50)->nullable();
            $table->json('gateway_response')->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'redeemed'])->default('pending');
            $table->date('redemption_date')->nullable();
            $table->string('redeem_to')->nullable();
            $table->timestamps();
        });

        Schema::create('e_hundi_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->string('transaction_id', 50)->unique();
            $table->string('qr_code')->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['upi', 'card', 'netbanking'])->default('upi');
            $table->string('upi_id', 100)->nullable();
            $table->date('transaction_date');
            $table->json('gateway_response')->nullable();
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->foreignId('donation_id')->nullable()->constrained('donations')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('e_hundi_transactions');
        Schema::dropIfExists('digital_gold_transactions');
        Schema::dropIfExists('donations');
        Schema::dropIfExists('receipts');
        Schema::dropIfExists('donors');
        Schema::dropIfExists('donation_categories');
    }
};
