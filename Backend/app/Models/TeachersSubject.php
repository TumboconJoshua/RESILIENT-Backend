<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeachersSubject extends Model
{
    protected $table = 'teachers_subject';

    // If your primary key is the default 'id', you don't need to specify it.
    // But if you want to specify, uncomment below:
    // protected $primaryKey = 'id';

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'subject_code',
    ];

    // If you want to disable timestamps, set this to false
    // public $timestamps = false;

    // Relationships

    public function teacher()
    {
        return $this->belongsTo(TeacherModel::class, 'teacher_id', 'Teacher_ID');
    }

    public function subject()
    {
        return $this->belongsTo(SubjectModel::class, 'subject_id', 'Subject_ID');
    }
}
