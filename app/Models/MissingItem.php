<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissingItem extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'details', 'img', 'found', 'found_by', 'created_by', 'updated_by'];

    protected $casts = [
        'img' => 'array'
    ];
}
