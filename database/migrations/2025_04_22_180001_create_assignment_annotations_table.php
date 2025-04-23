<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentAnnotationsTable extends Migration
{
    public function up()
    {
        Schema::create('assignment_annotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('page');
            $table->float('top');
            $table->float('left');
            $table->float('width');
            $table->float('height');
            $table->string('name');
            $table->unsignedBigInteger('assignment_id');
            $table->timestamps();

            $table->foreign('assignment_id')->references('id')->on('assignments')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('annotations');
    }
}
