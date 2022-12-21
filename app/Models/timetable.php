<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory;

    protected $fillable = ['class', 'room', 'data', 'created_by', 'updated_by'];

    protected $casts = [
        'data' => 'array'
    ];
}
