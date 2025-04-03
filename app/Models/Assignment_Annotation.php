<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment_Annotation extends Model
{
    protected $table = 'assignment_annotations';

    protected $fillable = [
        'page',
        'top',
        'left',
        'width',
        'height',
        'name',
        'assignment_id'
    ];
}
