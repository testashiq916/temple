<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->foreignId('category_id')->constrained('inventory_categories');
            $table->string('item_code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unit', 20)->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('min_quantity')->default(0);
            $table->integer('max_quantity')->default(0);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('total_value', 15, 2)->nullable();
            $table->string('location')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['available', 'low_stock', 'expired', 'disposed'])->default('available');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->foreignId('item_id')->constrained('inventory_items');
            $table->string('movement_id', 50)->unique();
            $table->enum('movement_type', ['purchase', 'issue', 'return', 'transfer', 'dispose']);
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('total_amount', 15, 2)->nullable();
            $table->date('movement_date');
            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable();
            $table->string('reference_no', 50)->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('inventory_categories');
    }
};
