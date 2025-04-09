<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Submission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'assignment_id',
        'user_id',
        'file_path',
    ];

    /**
     * Get the assignment this submission belongs to.
     */
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Get the user who made this submission.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
