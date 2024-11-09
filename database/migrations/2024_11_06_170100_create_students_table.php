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
            $table->id();
            $table->unsignedBigInteger('user_id');  // Foreign key for user table
            $table->unsignedBigInteger('instructor_id')->nullable();  // Foreign key for instructor table
            $table->unsignedBigInteger('ta_id')->nullable();  // Foreign key for TA table
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('instructor_id')->references('id')->on('instructors')->onDelete('set null');
            $table->foreign('ta_id')->references('id')->on('tas')->onDelete('set null');
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
