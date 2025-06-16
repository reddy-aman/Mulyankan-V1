<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSplitSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('split_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('roll_no')->nullable();
            $table->string('department')->nullable();
            $table->unsignedBigInteger('submission_id');
            $table->string('file_path');
            $table->timestamps();
    
            $table->foreign('submission_id')->references('id')->on('submissions')->onDelete('cascade');
            // $table->foreign('roll_no')->references('sid')->on('students')->onDelete('cascade');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('split_submissions');
    }
}
