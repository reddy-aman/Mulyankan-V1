<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SplitSubmission extends Model
{
    use HasFactory;

    protected $table = 'split_submissions';
    protected $fillable = [
        'submission_id',
        'roll_no',
        'department',
        'file_path',
    ];

    /**
     * The submission this split‐piece belongs to.
     */
    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * (Optional) If you link roll_no back to a Student model:
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'roll_no', 'sid');
    }
}
