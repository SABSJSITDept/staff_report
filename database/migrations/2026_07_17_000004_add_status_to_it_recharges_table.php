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
        Schema::table('it_recharges', function (Blueprint $table) {
            $table->enum('status', ['active', 'closed'])->default('active')->after('payment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('it_recharges', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
