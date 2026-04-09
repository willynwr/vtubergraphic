<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_swap_requests', function (Blueprint $table) {
            $table->date('target_date')->nullable()->after('requested_date');
        });
    }

    public function down(): void
    {
        Schema::table('schedule_swap_requests', function (Blueprint $table) {
            $table->dropColumn('target_date');
        });
    }
};
