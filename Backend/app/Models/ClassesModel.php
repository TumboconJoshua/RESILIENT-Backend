<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Models\TeacherModel;
use App\Models\SchoolYearModel;
use App\Models\ClassesModel;



class ClassesModel extends Model
{
    use HasFactory;

    protected $table = 'classes';
    protected $primaryKey = 'Class_ID';

    protected $fillable = [
        'ClassName',
        'Section',
        'SY_ID',
        'Grade_Level',
        'Track',
        'classType',
        'Teacher_ID',
        'Status',
        'Curriculum',
        'comments',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'Grade_Level' => 'string', // Explicitly cast the enum to string
    ];

    public function students()
    {
        return $this->hasMany(StudentClass::class, 'Class_ID');
    }

    public function getStudentCountByTrack()
    {
        return DB::table('students')
            ->where('Track', $this->Track)
            ->count();
    }

    public function subjects()
    {
        return $this->hasMany(ClassSubject::class, 'Class_ID');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYearModel::class, 'SY_ID');
    }

    public function adviser()
    {
        return $this->belongsTo(TeacherModel::class, 'Adviser_ID', 'Teacher_ID');
    }

}
