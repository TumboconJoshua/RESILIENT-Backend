<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClassModel extends Model
{
    use HasFactory;

    // Correct the table name
    protected $table = 'student_class';

    // Primary key
    protected $primaryKey = 'StudentClass_ID';

    // Fillable fields for mass assignment
    protected $fillable = [
        'Student_ID',
        'Class_ID',
        'SY_ID',
        'Teacher_ID',
        'ClassName',
        'isAdvisory',
        'Status',
        'created_at',
        'updated_at'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(StudentModel::class, 'Student_ID');
    }

    public function class()
    {
        return $this->belongsTo(ClassesModel::class, 'Class_ID');
    }

    public function teacher()
    {
        return $this->belongsTo(TeacherModel::class, 'Teacher_ID');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYearModel::class, 'SY_ID');
    }
}
