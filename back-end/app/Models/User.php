<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'date_of_birth',
        'gender',
        'phone',
        'photo',
        'email',
        'password',
    ];
    protected $appends=['role'];
    function getRoleAttribute()
    {
        return 'user';
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'gender'=>Gender::class,
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'created_at' => 'date:d-M-Y H:i',
        'date_of_birth' => 'date:d-M-Y',
    ];
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
