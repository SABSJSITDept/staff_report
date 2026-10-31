<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('daily_report_task_outputs', function (Blueprint $table) {
            $table->dropForeign('daily_report_task_outputs_staff_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_report_task_outputs', function (Blueprint $table) {
            $table->foreign('staff_id')->references('id')->on('staff_details')->onDelete('set null');
        });
    }
};

