<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->string('email')->unique()->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->string('timezone', 50)->default('Asia/Kolkata');
            $table->string('currency', 10)->default('INR');
            $table->string('date_format', 20)->default('Y-m-d');
            $table->string('logo_path')->nullable();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->enum('subscription_status', ['trial', 'active', 'suspended', 'cancelled', 'expired'])->default('trial');
            $table->date('subscription_start_date')->nullable();
            $table->date('subscription_end_date')->nullable();
            $table->integer('user_limit')->default(50);
            $table->integer('devotee_limit')->default(10000);
            $table->bigInteger('storage_limit')->default(5368709120);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
