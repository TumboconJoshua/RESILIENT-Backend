<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolYearModel extends Model
{
    use HasFactory;

    protected $table = 'school_years';  // <-- set your actual table name here
    protected $primaryKey = 'SY_ID';

    public function classes()
    {
        return $this->hasMany(ClassesModel::class, 'SY_ID'); // Also use ClassesModel if that's your class name
    }
}
