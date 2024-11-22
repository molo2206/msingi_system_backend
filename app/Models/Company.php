<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Company extends Model
{
    use HasApiTokens, HasFactory, HasUuids;
    protected $fillable =
    [
        'name',
        'description',
        'email',
        'phone',
        'adresse',
        'website',
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'id_nat',
        'rccm',
        'num_impot',
        'banner',
        'logo',
        'trial',
        'secteur_id',
        'nbr_users',
        'code',
        'country',
        'city',
        'longitude',
        'latitude',
    ];

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            User_has_company::class
        )->withPivot('fonction_id');
    }

    public function fonction()
    {
        return $this->belongsToMany(
            Fonction::class,
            User_has_company::class
        )->withPivot('user_id');
    }

    public function abonnement()
    {
        return $this->hasMany(Abonnement::class, 'company_id');
    }

    public function secteur(){
        return $this->belongsTo(SecteurActivity::class,'secteur_id');
    }
}
