<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daybook', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->unsignedBigInteger('sno');
            $table->string('accode', 20);
            $table->string('opaccode', 20);
            $table->decimal('amount', 15, 2);
            $table->enum('drcr', ['dr', 'cr']);
            $table->enum('voucher_type', ['donation', 'seva', 'payment', 'receipt', 'journal', 'contra', 'credit_note', 'debit_note']);
            $table->string('voucher_no', 50);
            $table->date('voucher_date');
            $table->text('remarks')->nullable();
            $table->string('reference_no', 50)->nullable();
            $table->date('reference_date')->nullable();
            $table->foreignId('devotee_id')->nullable()->constrained('devotees')->nullOnDelete();
            $table->foreignId('seva_booking_id')->nullable()->constrained('seva_bookings')->nullOnDelete();
            $table->foreignId('donation_id')->nullable()->constrained('donations')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign('accode')->references('accode')->on('accountm');
            $table->foreign('opaccode')->references('accode')->on('accountm');
            $table->index(['voucher_type', 'voucher_no'], 'idx_voucher');
            $table->index('accode', 'idx_account');
            $table->index('opaccode', 'idx_opposite');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daybook');
    }
};
