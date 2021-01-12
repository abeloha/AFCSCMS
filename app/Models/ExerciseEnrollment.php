<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class ExerciseEnrollment extends Model
{
    use SoftDeletes;
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'term_id',
        'session_id',
        'exercise_id',
        'oral_grade',
        'written_grade',
        'total_grade',
        'wp_grade',
        'ci_wp_grade',
        'dpty_cmd_wp_grade',
        'total_wp',
        'love_letter',
        'log',
    ];
}
