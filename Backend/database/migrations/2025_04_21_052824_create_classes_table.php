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
        Schema::create('classes', function (Blueprint $table) {
            $table->id('Class_ID');
            $table->string('ClassName');
            $table->string('Section');
            $table->unsignedBigInteger('SY_ID');
            $table->enum('Grade_Level', ['7','8','9','10','11','12']);
            $table->string('Track')->nullable(); // if applicable only to SHS
            $table->unsignedBigInteger('Adviser_ID')->nullable();
            $table->enum('Curriculum', ['JHS', 'SHS'])->nullable();
            $table->text('comments')->nullable();
            $table->enum('Status', ['Pending', 'Active', 'Inactive'])->default('Pending');
            $table->timestamps();
            $table->foreign('SY_ID')->references('SY_ID')->on('school_years')->onDelete('cascade');
            $table->foreign('Adviser_ID')->references('Teacher_ID')->on('teachers')->onDelete('set null');

            $table->index(['SY_ID', 'Grade_Level']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
