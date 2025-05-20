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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id('Teacher_ID');
            $table->string('EmployeeNo')->unique();
            $table->string('Email');
            $table->string('Password');
            $table->string('FirstName');
            $table->string('LastName');
            $table->string('MiddleName')->nullable();
            $table->string('Suffix')->nullable();
            $table->string('Educational_Attainment');
            $table->string('Teaching_Position');
            $table->date('BirthDate');
            $table->enum('Sex', ['M', 'F']);
            $table->enum('Position', ['Admin', 'Teacher', 'SuperAdmin']);
            $table->string('ContactNumber');
            $table->string('Address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
