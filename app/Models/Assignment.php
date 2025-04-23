<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'Name',
        'course_number',
        'points',
        'release_date',
        'due_date',
        'status',            // e.g., published or not
        'submissions_count', // optional
        'template_id',
        'type',
    ];

    protected $casts = [
        'release_date' => 'datetime',
    ];
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function annotations()
    {
        return $this->hasMany(Assignment_Annotation::class);
    }
}
