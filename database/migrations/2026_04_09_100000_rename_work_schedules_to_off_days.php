<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old FK on swap requests first
        if (Schema::hasColumn('schedule_swap_requests', 'work_schedule_id')) {
            Schema::table('schedule_swap_requests', function (Blueprint $table) {
                $table->dropForeign(['work_schedule_id']);
                $table->dropColumn('work_schedule_id');
            });
        }

        Schema::dropIfExists('work_schedules');

        // off_days stores the FIXED weekly schedule (day_of_week pattern)
        Schema::create('off_days', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->unsignedTinyInteger('day_of_week'); // 0=Sunday, 1=Monday, ..., 6=Saturday
            $table->timestamps();

            $table->foreign('employee_id')->references('employee_id')->on('employees')->cascadeOnDelete();
            $table->unique(['employee_id', 'day_of_week']);
        });

        // off_day_overrides stores per-date exceptions (swap results, special changes)
        Schema::create('off_day_overrides', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->date('override_date');
            $table->boolean('is_off'); // true = forced off, false = forced work (despite schedule)
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('employee_id')->on('employees')->cascadeOnDelete();
            $table->unique(['employee_id', 'override_date']);
            $table->index('override_date');
        });

        // Add off_day_id to swap requests (nullable, optional link)
        Schema::table('schedule_swap_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('off_day_id')->nullable()->after('employee_id');
        });
    }

    public function down(): void
    {
        Schema::table('schedule_swap_requests', function (Blueprint $table) {
            $table->dropColumn('off_day_id');
        });

        Schema::dropIfExists('off_day_overrides');
        Schema::dropIfExists('off_days');

        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->date('work_date');
            $table->string('shift_name')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('employee_id')->on('employees')->cascadeOnDelete();
            $table->unique(['employee_id', 'work_date']);
            $table->index(['work_date']);
        });

        Schema::table('schedule_swap_requests', function (Blueprint $table) {
            $table->foreignId('work_schedule_id')->nullable()->constrained('work_schedules')->nullOnDelete();
        });
    }
};
