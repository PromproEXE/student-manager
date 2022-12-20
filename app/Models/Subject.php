<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;
    protected $fillable = ['for_class', 'subject_id', 'name', 'department', 'teacher', 'created_by', 'updated_by'];

    protected $casts = [
        'for_class' => 'array',
        'teacher' => 'array'
    ];
}
