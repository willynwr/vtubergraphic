<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_swap_requests', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->foreignId('work_schedule_id')->nullable()->constrained('work_schedules')->nullOnDelete();
            $table->date('requested_date');
            $table->string('swap_with_employee_id')->nullable();
            $table->text('reason');
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->text('admin_note')->nullable();
            $table->string('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('employee_id')->on('employees')->cascadeOnDelete();
            $table->foreign('swap_with_employee_id')->references('employee_id')->on('employees')->nullOnDelete();
            $table->index(['employee_id', 'requested_date']);
            $table->index(['status', 'requested_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_swap_requests');
    }
};