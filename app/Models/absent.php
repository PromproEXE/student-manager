<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absent extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'type', 'to', 'from', 'details', 'approve', 'created_by'];
}
