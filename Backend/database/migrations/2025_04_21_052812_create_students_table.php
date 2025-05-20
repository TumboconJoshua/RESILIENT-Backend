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
        Schema::create('students', function (Blueprint $table) {
            $table->id('Student_ID');
            $table->string('LRN')->unique();
            $table->enum('Grade_Level', ['7','8','9','10','11','12']);
            $table->string('FirstName');
            $table->string('LastName');
            $table->string('MiddleName')->nullable();
            $table->enum('Suffix', ['Jr.', 'Sr.', 'II', 'III']);
            $table->date('BirthDate');
            $table->enum('Sex', ['M', 'F']);
            $table->string('Age');
            $table->string('Religion')->nullable();
            $table->string('HouseNo');
            $table->string('Barangay');
            $table->string('Municipality');
            $table->string('Province');
            $table->string('MotherName');
            $table->string('FatherName');
            $table->string('Guardian');
            $table->string('Relationship');
            $table->string('ContactNumber');
            $table->enum('Curriculum', ['JHS', 'SHS']);
            $table->string('Track');
            $table->timestamps();
            $table->enum('Status', ['Pending', 'Accepted', 'Declined']);
            $table->string('comments')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
