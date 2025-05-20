<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Models\Research;

class TeacherModel extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'teachers';
    protected $primaryKey = 'Teacher_ID';

    protected $fillable = [
        'Teacher_ID', 
        'EmployeeNo',
        'Email',
        'Password',
        'FirstName',
        'LastName',
        'MiddleName',
        'BirthDate',
        'Sex',
        'Position',
        'ContactNumber',
        'Address',
        'Research',
        'Avatar',
    ];

    protected $hidden = [
        'Password',
    ];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teachers_subject', 'teacher_id', 'subject_id');
    }


    public function classes()
    {
        return $this->hasMany(Classes::class, 'Teacher_ID');
    }

    public function researches()
    {
        return $this->hasMany(Research::class, 'Teacher_ID');
    }

    public function lessonPlans()
    {
        return $this->hasMany(LessonPlan::class, 'Teacher_ID');
    }

    public function getAuthPassword()
    {
        return $this->Password;
    }

    public function getEmailForPasswordReset()
    {
        return $this->Email;
    }
}

