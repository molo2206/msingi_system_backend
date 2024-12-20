<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Company extends Model
{
    use HasApiTokens, HasFactory, HasUuids;
    protected $appends = ["modules"];
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
        return $this->hasMany(
            User_has_company::class,
            'company_id',
            'id'
        );
    }
    public function employees()
    {
        return $this->hasMany(User_has_company::class);
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
        return $this->belongsToMany(
            Plans::class,
            'abonnements',
            'company_id',
            'plans_id',
        )->withPivot(['company_id', 'token', 'expires', 'plans_id', 'total_price']);;
    }

    public function abonnements()
    {
        return $this->hasMany(Abonnement::class, 'company_id', 'id');
    }

    public function modules()
    {
        return $this->hasManyThrough(
            Modules::class,
            Plans::class,
            'id',
            'id',
            'id',
            'plans_id'
        )->join('abonnements', 'plans.id', '=', 'abonnements.plans_id')
            ->join('plan_has_modules', 'plans.id', '=', 'plan_has_modules.plan_id')
            ->where('abonnements.company_id', $this->id);
    }

    public function getModulesAttribute()
    {
        $abonnement = $this->abonnements()->with('plan.modules.ressource')->orderBy('created_at', 'desc')->first();
        return $abonnement;
    }

    public function secteur()
    {
        return $this->belongsTo(SecteurActivity::class, 'secteur_id');
    }

    public function succursale()
    {
        return $this->hasMany(Succursales::class, 'company_id', 'id');
    }
}
