<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    use HasFactory;

    protected $primaryKey = 'SY_ID';

    public function classes()
    {
        return $this->hasMany(Classes::class, 'SY_ID');
    }
}
