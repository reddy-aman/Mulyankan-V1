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
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->integer('points')->nullable();
            $table->dateTime('release_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->boolean('status')->default(true);    // e.g., published/active status
            $table->integer('submissions_count')->default(0);
            $table->timestamps();

            // Assuming your courses table is named "courses" with primary key "id"
            $table->foreign('course_id')
                  ->references('id')
                  ->on('courses')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('assignments');
    }
};
// This should run after creation of courses as it links to the courses using forign key.