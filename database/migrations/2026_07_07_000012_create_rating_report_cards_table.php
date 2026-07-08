<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rating_report_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->foreignId('category_id')->constrained('rating_categories')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('rating_questions')->onDelete('cascade');
            $table->integer('rating');
            $table->unsignedBigInteger('rating_given_by_id');
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rating_report_cards');
    }
};
