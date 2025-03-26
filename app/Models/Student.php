<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    // Fillable fields based on a migration that includes name, email, user_id, and course_number.
    protected $table = 'students';
    protected $fillable = [
        'name',
        'email',
        'user_id',
        'course_number',
        'sid',
    ];

    /**
     * A student belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A student may belong to an instructor.
     */
    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    /**
     * A student may belong to a TA.
     */
    public function ta()
    {
        return $this->belongsTo(Ta::class);
    }

    /**
     * A student belongs to a course.
     * We assume the foreign key on this model is 'course_number' and it matches the 'course_number' field on the courses table.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_number', 'course_number');
    }

    public $timestamps = true;
}
