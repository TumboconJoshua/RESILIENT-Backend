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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id('Subject_ID');
            $table->string('SubjectName');
            $table->integer('SubjectCode');
            $table->string('Track');
            $table->unsignedBigInteger('Teacher_ID');
            $table->timestamps();
        
            $table->foreign('Teacher_ID')->references('Teacher_ID')->on('teachers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
