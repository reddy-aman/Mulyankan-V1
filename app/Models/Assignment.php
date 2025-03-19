<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'points',
        'release_date',
        'due_date',
        'status',            // e.g., published or not
        'submissions_count', // optional
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
