<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{
    use HasFactory;
    protected $fillable=[
        'question_text',
        'difficulty',
        'chapitre_id',
        'user_id',
        'reponses'
    ];
}
