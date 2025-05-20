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
        Schema::create('class_subject', function (Blueprint $table) {
            $table->id('StudentClassSub_ID');
            $table->unsignedBigInteger('Student_ID');
            $table->unsignedBigInteger('Class_ID');
            $table->unsignedBigInteger('SY_ID');
            $table->unsignedBigInteger('Teacher_ID');
            $table->unsignedBigInteger('Subject_ID');
            $table->timestamps();

            $table->foreign('Student_ID')->references('Student_ID')->on('students')->onDelete('cascade');
            $table->foreign('Class_ID')->references('Class_ID')->on('classes')->onDelete('cascade');
            $table->foreign('SY_ID')->references('SY_ID')->on('school_years')->onDelete('cascade');
            $table->foreign('Teacher_ID')->references('Teacher_ID')->on('teachers')->onDelete('cascade');
            $table->foreign('Subject_ID')->references('Subject_ID')->on('subjects')->onDelete('cascade');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_subject');
    }
};
