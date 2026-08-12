<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seva_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->string('name');
            $table->string('sanskrit_name')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('seva_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->foreignId('category_id')->constrained('seva_categories');
            $table->string('seva_id', 50)->unique();
            $table->string('name');
            $table->string('sanskrit_name')->nullable();
            $table->text('description')->nullable();
            $table->text('procedure')->nullable();
            $table->integer('duration_minutes')->default(30);
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('gst_rate', 5, 2)->default(18);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->integer('max_devotees')->default(1);
            $table->integer('min_advance_days')->default(0);
            $table->integer('max_advance_days')->default(365);
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_approval')->default(true);
            $table->string('image_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('seva_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seva_id')->constrained('seva_services')->cascadeOnDelete();
            $table->date('slot_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('capacity')->default(1);
            $table->integer('booked_count')->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->unique(['seva_id', 'slot_date', 'start_time'], 'unique_slot');
        });

        Schema::create('seva_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->string('booking_id', 50)->unique();
            $table->foreignId('devotee_id')->constrained();
            $table->foreignId('seva_id')->constrained('seva_services');
            $table->foreignId('slot_id')->constrained('seva_slots');
            $table->date('booking_date');
            $table->date('slot_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('number_of_devotees')->default(1);
            $table->json('devotee_names')->nullable();
            $table->text('special_requests')->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->nullable();
            $table->decimal('net_amount', 10, 2)->nullable();
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'refunded', 'failed'])->default('pending');
            $table->unsignedBigInteger('receipt_id')->nullable();
            $table->unsignedBigInteger('voucher_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign('voucher_id')->references('id')->on('vouchers')->nullOnDelete();
        });

        Schema::create('prasad_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->string('booking_id', 50)->unique();
            $table->foreignId('devotee_id')->constrained();
            $table->string('prasad_type', 100);
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->date('booking_date');
            $table->date('collection_date')->nullable();
            $table->time('collection_time')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'collected', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prasad_bookings');
        Schema::dropIfExists('seva_bookings');
        Schema::dropIfExists('seva_slots');
        Schema::dropIfExists('seva_services');
        Schema::dropIfExists('seva_categories');
    }
};
