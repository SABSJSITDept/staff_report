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
        Schema::table('it_tickets', function (Blueprint $table) {
            $table->dropColumn('photo');
            $table->json('photos')->nullable()->after('issue_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('it_tickets', function (Blueprint $table) {
            $table->dropColumn('photos');
            $table->string('photo')->nullable()->after('issue_description');
        });
    }
};
