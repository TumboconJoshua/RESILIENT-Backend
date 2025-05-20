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
        Schema::create('teachers_subject', function (Blueprint $table) {
            $table->id();

            // Use unsignedBigInteger for manual foreign keys
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('subject_id');

            // Foreign key definitions
            $table->foreign('teacher_id')->references('Teacher_ID')->on('teachers')->onDelete('cascade');
            $table->foreign('subject_id')->references('Subject_ID')->on('subjects')->onDelete('cascade');

            $table->string('subject_code');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers_subject');
    }
};
