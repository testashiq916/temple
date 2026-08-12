<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temple_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->string('property_id', 50)->unique();
            $table->string('name');
            $table->enum('property_type', ['land', 'building', 'commercial', 'residential', 'agricultural', 'mixed']);
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('total_area', 15, 2)->nullable();
            $table->string('area_unit', 20)->default('sq_meter');
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->decimal('current_value', 15, 2)->nullable();
            $table->date('valuation_date')->nullable();
            $table->string('title_deed_number', 100)->nullable();
            $table->string('survey_number', 100)->nullable();
            $table->string('document_path')->nullable();
            $table->enum('status', ['active', 'inactive', 'under_maintenance', 'disposed'])->default('active');
            $table->enum('ownership_type', ['temple', 'trust', 'lease'])->default('temple');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('land_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->foreignId('property_id')->constrained('temple_properties');
            $table->string('record_id', 50)->unique();
            $table->string('survey_number', 100)->nullable();
            $table->string('khata_number', 100)->nullable();
            $table->string('plot_number', 100)->nullable();
            $table->string('village', 100)->nullable();
            $table->string('tehsil', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->decimal('area_hectares', 15, 2)->nullable();
            $table->string('soil_type', 100)->nullable();
            $table->string('irrigation_type', 100)->nullable();
            $table->text('crop_details')->nullable();
            $table->date('record_date')->nullable();
            $table->string('document_path')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('property_tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->foreignId('property_id')->constrained('temple_properties');
            $table->string('tenant_id', 50)->unique();
            $table->string('full_name');
            $table->string('contact_person')->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('business_type', 100)->nullable();
            $table->string('business_license_number', 100)->nullable();
            $table->enum('status', ['active', 'inactive', 'evicted'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_tenants');
        Schema::dropIfExists('land_records');
        Schema::dropIfExists('temple_properties');
    }
};
