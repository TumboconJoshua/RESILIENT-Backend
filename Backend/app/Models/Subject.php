<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'subjects';
    protected $primaryKey = 'Subject_ID';

    protected $fillable = [
        'SubjectName',
        'SubjectCode',
        'Track',
        'Teacher_ID'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'Teacher_ID');
    }
}