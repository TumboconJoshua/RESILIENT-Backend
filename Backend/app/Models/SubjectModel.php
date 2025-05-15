<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubjectGrade;

class SubjectModel extends Model
{
    use HasFactory;

    protected $primaryKey = 'Subject_ID';
    protected $table = 'subjects';

    public function grades()
    {
        return $this->hasMany(SubjectGrade::class, 'Subject_ID');
    }

    public function teacher()
    {
        return $this->belongsTo(TeacherModel::class, 'Teacher_ID');
    }
}
