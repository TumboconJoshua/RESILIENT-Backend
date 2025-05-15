<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSubject extends Model
{
    use HasFactory;

    protected $table = 'class_subject';
    protected $primaryKey = 'StudentClassSub_ID';

    protected $fillable = [
        'Student_ID',
        'Class_ID',
        'SY_ID',
        'Teacher_ID',
        'Subject_ID'
    ];

    public function student()
    {
        return $this->belongsTo(StudentModel::class, 'Student_ID');
    }

    public function class()
    {
        return $this->belongsTo(ClassesModel::class, 'Class_ID');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class, 'SY_ID');
    }

    public function teacher()
    {
        return $this->belongsTo(TeacherModel::class, 'Teacher_ID');
    }

    public function subject()
    {
        return $this->belongsTo(SubjectModel::class, 'Subject_ID');
    }
}
