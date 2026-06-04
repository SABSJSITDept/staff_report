<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_defaulter_mail_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('sent_by')->nullable();
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('staff_details')->onDelete('cascade');
            $table->foreign('sent_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_defaulter_mail_logs');
    }
};
