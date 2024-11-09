<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['user_id', 'instructor_id', 'ta_id'];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function ta()
    {
        return $this->belongsTo(TA::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public $timestamps = true;
}
