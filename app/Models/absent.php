<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absent extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'user_name', 'type', 'to', 'from', 'details', 'approve', 'approve_at', 'approve_by', 'created_by'];
}
