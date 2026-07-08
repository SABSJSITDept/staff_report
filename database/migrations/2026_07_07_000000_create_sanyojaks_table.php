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
        Schema::create('sanyojaks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('pravarti')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->json('staff_assigned')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanyojaks');
    }
};
