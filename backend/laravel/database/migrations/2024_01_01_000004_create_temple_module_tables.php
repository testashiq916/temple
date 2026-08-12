<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('temple_id', 50)->unique();
            $table->string('name');
            $table->string('sanskrit_name')->nullable();
            $table->text('description')->nullable();
            $table->text('history')->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->integer('established_year')->nullable();
            $table->string('deity_name')->nullable();
            $table->text('deity_description')->nullable();
            $table->enum('temple_type', ['jyotirlinga', 'shaktipeeth', 'divyadesam', 'pancha_linga', 'vaishno', 'other'])->default('other');
            $table->integer('capacity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('profile_image')->nullable();
            $table->string('banner_image')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('temple_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temple_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->string('image_name')->nullable();
            $table->enum('image_type', ['gallery', 'deity', 'event', 'facility'])->default('gallery');
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('deities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->string('deity_id', 50)->unique();
            $table->string('name');
            $table->string('sanskrit_name')->nullable();
            $table->text('description')->nullable();
            $table->string('avatar', 100)->nullable();
            $table->string('consort_name')->nullable();
            $table->string('vehicle')->nullable();
            $table->string('color', 50)->nullable();
            $table->text('mantra')->nullable();
            $table->text('significance')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('festivals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->string('festival_id', 50)->unique();
            $table->string('name');
            $table->string('sanskrit_name')->nullable();
            $table->text('description')->nullable();
            $table->text('significance')->nullable();
            $table->enum('festival_type', ['annual', 'monthly', 'weekly', 'special'])->default('annual');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('hijri_date', 50)->nullable();
            $table->boolean('is_recurring')->default(true);
            $table->json('recurrence_pattern')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('festivals');
        Schema::dropIfExists('deities');
        Schema::dropIfExists('temple_images');
        Schema::dropIfExists('temples');
    }
};
