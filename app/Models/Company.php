<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory, HasUuids;
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
        'code'
    ];

    public function users(){
        return $this->belongsToMany(User::class,
        User_has_company::class)->withPivot('fonction_id');
    }

    public function fonction(){
        return $this->belongsToMany(Fonction::class,
        User_has_company::class)->withPivot('user_id');
    }

}
