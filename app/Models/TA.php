<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ta extends Model
{
    protected $table = 'tas';

    protected $fillable = [
        'name',
        'email',
        'user_id',
        'course_id',
        'email_notified',
    ];

    /**
     * The TA belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The TA belongs to a course.
     * Again, 'course_number' is used to relate to the courses table.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_number', 'course_number');
    }

    /**
     * Optionally, if TAs are associated with students:
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public $timestamps = true;
}
