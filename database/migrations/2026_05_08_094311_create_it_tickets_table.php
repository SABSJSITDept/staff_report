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
        Schema::create('it_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('users');
            $table->foreignId('it_staff_id')->nullable()->constrained('users');
            $table->enum('category', ['Hardware', 'Software']);
            $table->string('subject');
            $table->text('issue_description');
            $table->string('photo')->nullable();
            $table->enum('status', ['Pending', 'In Progress', 'Completed', 'Paused'])->default('Pending');
            $table->dateTime('expected_arrival_time')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('it_tickets');
    }
};
