<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSubjectModel extends Model
{
    use HasFactory;

    protected $table = 'class_subject';
    protected $primaryKey = 'StudentClassSub_ID';

    public function subject()
    {
        return $this->belongsTo(SubjectModel::class, 'Subject_ID');
    }

    public function class()
    {
        return $this->belongsTo(ClassesModel::class, 'Class_ID');
    }

    public function teacher()
    {
        return $this->belongsTo(TeacherModel::class, 'Teacher_ID');
    }
    
}
