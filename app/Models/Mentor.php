<?php

namespace App\Models;

use Database\Factories\MentorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mentor extends Model
{
    /** @use HasFactory<MentorFactory> */
    use HasFactory;

    protected $fillable = [
        'full_name',
        'phone',
        'commune',
    ];
}
