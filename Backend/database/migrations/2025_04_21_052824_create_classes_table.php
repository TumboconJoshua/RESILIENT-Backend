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
            $table->string('Track');
            $table->enum('classType', ['Advisory', 'Regular']);
            $table->unsignedBigInteger('Teacher_ID');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        
            $table->foreign('SY_ID')->references('SY_ID')->on('school_years');
            $table->foreign('Teacher_ID')->references('Teacher_ID')->on('teachers');
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
