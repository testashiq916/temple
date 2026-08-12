<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('darshan_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->string('queue_id', 50)->unique();
            $table->enum('queue_type', ['general', 'special', 'vip', 'divyang'])->default('general');
            $table->dateTime('start_time');
            $table->integer('estimated_wait_time')->default(0);
            $table->integer('actual_wait_time')->default(0);
            $table->integer('total_devotees')->default(0);
            $table->integer('current_position')->default(0);
            $table->enum('status', ['active', 'paused', 'closed'])->default('active');
            $table->timestamps();
        });

        Schema::create('queue_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('queue_id')->constrained('darshan_queues')->cascadeOnDelete();
            $table->foreignId('devotee_id')->nullable()->constrained('devotees')->nullOnDelete();
            $table->string('queue_number', 50);
            $table->dateTime('entry_time');
            $table->dateTime('exit_time')->nullable();
            $table->enum('status', ['waiting', 'entered', 'completed', 'exited'])->default('waiting');
            $table->string('qr_code')->nullable();
            $table->string('wristband_id', 50)->nullable();
            $table->integer('darshan_duration')->default(0);
            $table->timestamps();
        });

        Schema::create('crowd_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->date('analytics_date');
            $table->integer('hour')->default(0);
            $table->integer('total_devotees')->default(0);
            $table->integer('predicted_devotees')->default(0);
            $table->decimal('peak_capacity_percent', 5, 2)->default(0);
            $table->integer('average_darshan_time')->default(0);
            $table->integer('average_queue_length')->default(0);
            $table->enum('congestion_level', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->json('heatmap_data')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->unique(['temple_id', 'analytics_date', 'hour'], 'unique_analytics');
        });

        Schema::create('cctv_cameras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->string('camera_id', 50)->unique();
            $table->string('name');
            $table->string('location')->nullable();
            $table->enum('camera_type', ['fixed', 'ptz', 'ai'])->default('fixed');
            $table->string('rtsp_url')->nullable();
            $table->boolean('ai_enabled')->default(false);
            $table->json('model_config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ai_detection_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('temple_id')->constrained();
            $table->foreignId('camera_id')->constrained('cctv_cameras');
            $table->enum('alert_type', ['crowd_surge', 'queue_length', 'missing_person', 'distress', 'security']);
            $table->enum('severity', ['info', 'warning', 'critical'])->default('info');
            $table->text('description')->nullable();
            $table->json('detection_data')->nullable();
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_detection_alerts');
        Schema::dropIfExists('cctv_cameras');
        Schema::dropIfExists('crowd_analytics');
        Schema::dropIfExists('queue_entries');
        Schema::dropIfExists('darshan_queues');
    }
};
