<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Chapitre extends Model
{
    use HasFactory;
    protected $fillable=[
        'title',
        'module_id'
    ];
    protected $casts=[
        'created_at' => 'date:d-M-Y H:i',
    ];
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
