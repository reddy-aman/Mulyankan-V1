<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    protected $table = 'instructors';

    protected $fillable = [
        'name',
        'email',
        'user_id',
        'course_id',
        'term',
        'year',
        'email_notified',
    ];

    /**
     * The instructor belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The instructor belongs to a course.
     * Here we assume the local 'course_number' references the 'course_number' column on the courses table.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_number', 'course_number');
    }

    /**
     * An instructor may have many students.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * An instructor may have many TAs.
     */
    public function tas()
    {
        return $this->hasMany(Ta::class);
    }

    public $timestamps = true;
}
