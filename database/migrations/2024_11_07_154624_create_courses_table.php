<?php
// database/migrations/xxxx_xx_xx_create_courses_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_number', 256)->unique(); // e.g., AI201
            $table->string('entry_code', 6)->unique()->nullable(); // 6-character code
            $table->string('course_name');
            $table->text('course_description');
            $table->string('term')->nullable();
            $table->string(column: 'year')->nullable();
            $table->string('department')->nullable();
            $table->foreignId('instructor_id')->constrained('users'); // Assuming instructor is stored in users table
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
