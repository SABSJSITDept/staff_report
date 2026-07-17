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
            $table->string('payment_type')->default('Manual')->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('it_recharges', function (Blueprint $table) {
            $table->dropColumn('payment_type');
        });
    }
};
