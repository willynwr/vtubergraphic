<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->enum('type', ['IN', 'OUT', 'IZIN', 'SAKIT', 'TUKAR_LIBUR']);
            $table->date('date');
            $table->time('time');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('distance_meters', 10, 2)->nullable();
            $table->text('note')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
            $table->index(['employee_id', 'date']);
            $table->index(['date', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
