<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ClassSubjectModel;
use App\Models\ClassSubject;
use App\Models\SubjectModel;
use App\Models\TeacherModel;

class ClassesModel extends Model
{
    use HasFactory;

    protected $table = 'classes';
    protected $primaryKey = 'Class_ID';

    public function students()
    {
        return $this->hasMany(StudentClassModel::class, 'Class_ID');
    }

    public function subjects()
    {
        return $this->hasMany(ClassSubject::class, 'Class_ID');
    }

    public function teacherSubjects()
    {
        return $this->hasManyThrough(
            SubjectModel::class,  // Target model (what you want to access)
            TeacherModel::class,  // Intermediate model (pivot)
            'Teacher_ID',           // Foreign key on intermediate model (TeacherModel)
            'Teacher_ID',           // Foreign key on target model (SubjectModel)
            'Teacher_ID',           // Local key on current model (ClassesModel)
            'Teacher_ID'            // Local key on intermediate model (TeacherModel)
        );
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class, 'SY_ID');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'Teacher_ID');
    }
}
