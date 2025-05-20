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
        Schema::create('student_class', function (Blueprint $table) {
            $table->id('StudentClass_ID');
            $table->unsignedBigInteger('Student_ID');
            $table->unsignedBigInteger('Class_ID');
            $table->string('ClassName');
            $table->string('SY_ID');
            $table->unsignedBigInteger('Teacher_ID')->nullable();
            $table->boolean('isAdvisory')->default(false);
            $table->timestamps();

            $table->foreign('Student_ID')->references('Student_ID')->on('students');
            $table->foreign('Class_ID')->references('Class_ID')->on('classes');
            $table->foreign('Teacher_ID')->references('Teacher_ID')->on('teachers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_class');
    }
};
