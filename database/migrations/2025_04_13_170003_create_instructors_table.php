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
        Schema::create('instructors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->unsignedBigInteger('user_id')->nullable();  // Foreign key for user table
            $table->unsignedBigInteger('course_id');
            $table->boolean('email_notified')->default(false);
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');

            $table->unique(
                ['course_id', 'email'],
                'instructor_unique_courseid_email'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            $table->dropUnique('instructor_unique_courseid_email');
        });
        Schema::dropIfExists('instructors');
    }
};
