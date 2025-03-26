<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'file_path',
        'annotation_data',
    ];

    protected $casts = [
        'annotation_data' => 'array', // If you want automatic JSON cast
    ];
}
