<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roster extends Model
{
    use HasFactory;

    protected $table = 'roster';

    protected $fillable = [
        'name',
        'email',
        'user_id',
        'course_id',
        'role',
        'sid',
    ];

    // Relationship: Each roster entry belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship: Each roster entry belongs to a course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    // Scopes for different roles
    public function scopeInstructors($query, $courseId)
    {
        return $query->forCourse($courseId)->where('role', 2);
    }

    public function scopeStudents($query, $courseId)
    {
        return $query->forCourse($courseId)->where('role', 1);
    }

    public function scopeTAs($query, $courseId)
    {
        return $query->forCourse($courseId)->where('role', 3);
    }
}
