<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devotee_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->string('name', 100);
            $table->string('sanskrit_name')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('devotees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->string('devotee_id', 50)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('devotee_type_id')->nullable()->constrained('devotee_types');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('sanskrit_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth', 100)->nullable();
            $table->string('gotra', 100)->nullable();
            $table->string('rashi', 50)->nullable();
            $table->string('nakshatra', 50)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('email');
            $table->string('mobile', 20);
            $table->string('alternate_mobile', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->string('occupation')->nullable();
            $table->string('employer')->nullable();
            $table->string('profile_image')->nullable();
            $table->integer('family_members')->default(1);
            $table->boolean('is_member')->default(false);
            $table->string('membership_type', 50)->nullable();
            $table->date('membership_start_date')->nullable();
            $table->date('membership_end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('devotee_family', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devotee_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('sanskrit_name')->nullable();
            $table->string('relationship', 50);
            $table->date('date_of_birth')->nullable();
            $table->string('gotra', 100)->nullable();
            $table->string('rashi', 50)->nullable();
            $table->string('nakshatra', 50)->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('devotee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devotee_id')->constrained()->cascadeOnDelete();
            $table->string('document_type', 50);
            $table->string('document_name');
            $table->string('document_path');
            $table->string('document_number', 100)->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('devotee_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devotee_id')->constrained()->cascadeOnDelete();
            $table->string('preference_type', 50);
            $table->text('preference_value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devotee_preferences');
        Schema::dropIfExists('devotee_documents');
        Schema::dropIfExists('devotee_family');
        Schema::dropIfExists('devotees');
        Schema::dropIfExists('devotee_types');
    }
};
