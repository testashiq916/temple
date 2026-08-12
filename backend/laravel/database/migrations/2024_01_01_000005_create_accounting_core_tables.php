<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accountgbs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('bshead', 50)->unique();
            $table->string('bshead_name', 100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('accountg', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('grcode', 20)->unique();
            $table->string('grname', 100);
            $table->foreignId('bshead_id')->constrained('accountgbs');
            $table->string('parent_grcode', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('parent_grcode')->references('grcode')->on('accountg')->nullOnDelete();
        });

        Schema::create('accountm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('accode', 20)->unique();
            $table->string('name');
            $table->string('grcode', 20);
            $table->string('bshead', 50);
            $table->enum('actype', ['debit', 'credit']);
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('is_default')->default(false);
            $table->text('address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('tax_number', 50)->nullable();
            $table->enum('gst_type', ['registered', 'unregistered', 'composition'])->default('unregistered');
            $table->string('gstin', 50)->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_ifsc', 20)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign('grcode')->references('grcode')->on('accountg');
        });

        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('voucher_no', 50);
            $table->enum('voucher_type', ['donation', 'seva', 'payment', 'receipt', 'journal', 'contra', 'credit_note', 'debit_note']);
            $table->date('voucher_date');
            $table->string('reference_no', 50)->nullable();
            $table->date('reference_date')->nullable();
            $table->text('narration')->nullable();
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->boolean('is_posted')->default(false);
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['company_id', 'voucher_type', 'voucher_no'], 'unique_voucher');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('accountm');
        Schema::dropIfExists('accountg');
        Schema::dropIfExists('accountgbs');
    }
};
