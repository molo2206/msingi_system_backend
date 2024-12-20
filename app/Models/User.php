<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'prename',
        'last_name',
        'gender',
        'code',
        'phone',
        'email',
        'address',
        'country',
        'city',
        'date_of_birth',
        'profile',
        'password',
        'provider',
        'email_verified_at',
        'google2fa_secret',
        'longitude',
        'latitude',
        'type',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'company'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function company()
    {
        return $this->belongsToMany(
            Company::class,
            'user_has_companies'
        )->withPivot('fonction_id','company_id','user_id');
    }

    public function fonction()
    {
        return $this->belongsToMany(
            Fonction::class,
            'user_has_companies'
        )->withPivot('company_id');
    }



    public function userverify()
    {
        return $this->hasOne(User_verify::class, 'user_id');
    }



}
