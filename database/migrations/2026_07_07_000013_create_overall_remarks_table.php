<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('overall_remarks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->text('remark');
            $table->unsignedBigInteger('remark_given_by_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overall_remarks');
    }
};
