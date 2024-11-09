<?php
// app/Models/Course.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_number',
        'entry_code',
        'course_name',
        'course_description',
        'term',
        'year',
        'department',
        'instructor_id',
    ];

    // Relationships
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function tas()
    {
        return $this->belongsToMany(User::class, 'course_tas');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_students');
    }

    // Generate unique course number and entry code
    public static function generateUniqueCode()
    {
        return strtoupper(Str::random(3) . rand(100, 999));
    }
}
