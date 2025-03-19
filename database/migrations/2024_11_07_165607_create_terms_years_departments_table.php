<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTermsYearsDepartmentsTable extends Migration
{
    public function up()
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Type of the attribute, e.g., 'term', 'year', 'department'
            $table->string('value'); // Value of the attribute, e.g., 'Spring', '2024', 'Computer Science'
            $table->timestamps();

            // Enforcing uniqueness to avoid duplicates for a particular type and value
            $table->unique(['type', 'value']);

            // Optionally, you may also want to add indexes on 'type' and 'value' columns for better query performance:
            $table->index('type');
            $table->index('value');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attributes');
    }
}
