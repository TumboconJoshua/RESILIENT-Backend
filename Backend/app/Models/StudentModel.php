<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentModel extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $primaryKey = 'Student_ID';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'LRN',
        'Grade_Level',
        'FirstName',
        'LastName',
        'MiddleName',
        'Suffix',
        'BirthDate',
        'Sex',
        'Age',
        'Religion',
        'HouseNo',
        'Barangay',
        'Municipality',
        'Province',
        'MotherName',
        'FatherName',
        'Guardian',
        'Relationship',
        'ContactNumber',
        'Curriculum',
        'Track',
        'Status',
        'comments',
    ];

    public function grades()
    {
        return $this->hasMany(SubjectGradeModel::class, 'Student_ID');
    }

    public function studentClasses()
    {
        return $this->hasMany(StudentClassModel::class, 'Student_ID');
    }
}
