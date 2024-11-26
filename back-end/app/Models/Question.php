<?php

namespace App\Models;

use App\Enums\QuestionDifficulty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Question extends Model
{
    use HasFactory;
    protected $fillable=[
        'question_text',
        'difficulty',
        'chapitre_id',
        'user_id',
        'confirme',
        'type'
    ];
    protected $casts=[
        'question_difficulty'=>QuestionDifficulty::class,
        'created_at' => 'date:d-M-Y H:i',

    ];
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }
    public function chapitre()
    {
        return $this->belongsTo(Chapitre::class);
    }
    public function reponses()
    {
        return $this->hasMany(Reponse::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
