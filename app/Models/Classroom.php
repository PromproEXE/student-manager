<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = ['class_id', 'name', 'for_class', 'department', 'teacher', 'data', 'created_by', 'updated_by'];
    protected $casts = [
        'for_class' => 'array',
        'teacher' => 'array',
        'data' => 'array'
    ];
}
