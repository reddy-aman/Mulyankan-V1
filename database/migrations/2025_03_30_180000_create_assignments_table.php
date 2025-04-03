<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('Name');
            $table->string('course_number');
            $table->integer('points')->nullable();
            $table->dateTime('release_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->boolean('status')->default(false);    // e.g., published/active status
            $table->integer('submissions_count')->default(0);
            $table->unsignedBigInteger('template_id');
            $table->string('type');
            $table->timestamps();

            // Assuming your courses table is named "courses" with primary key "id"
            $table->foreign('course_number')->references('course_number')->on('courses')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('templates')->onDelete('cascade');

        });
    }

    public function down()
    {
        Schema::dropIfExists('assignments');
    }
};
// This should run after creation of courses as it links to the courses using forign key.