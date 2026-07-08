<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('rating_report_cards', function (Blueprint $table) {
            $table->string('financial_year')->nullable()->after('rating_given_by_id');
        });

        Schema::table('overall_remarks', function (Blueprint $table) {
            $table->string('financial_year')->nullable()->after('remark_given_by_id');
        });
    }

    public function down()
    {
        Schema::table('rating_report_cards', function (Blueprint $table) {
            $table->dropColumn('financial_year');
        });

        Schema::table('overall_remarks', function (Blueprint $table) {
            $table->dropColumn('financial_year');
        });
    }
};
