<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Module extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'description',
    ];
    protected $casts=[
        'created_at' => 'date:d-M-Y H:i',
    ];
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }
    public function chapitres()
    {
        return $this->hasMany(Chapitre::class);
    }

}
