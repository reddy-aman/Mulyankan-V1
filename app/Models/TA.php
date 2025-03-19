<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TA extends Model
{
    protected $fillable = ['user_id'];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public $timestamps = true;
}
