<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClassModel extends Model
{
    use HasFactory;

    protected $table = 'student_class';
    protected $primaryKey = 'StudentClass_ID';

    public function student()
    {
        return $this->belongsTo(Student::class, 'Student_ID');
    }

    public function class()
    {
        return $this->belongsTo(ClassesModel::class, 'Class_ID');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class, 'SY_ID');
    }
}
