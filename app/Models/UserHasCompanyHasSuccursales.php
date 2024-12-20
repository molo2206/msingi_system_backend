<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserHasCompanyHasSuccursales extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['succursale_id', 'hasuser_id'];

    public function permission()
    {
        return $this->belongsToMany(Ressources::class, 'ressource__has__permissions', 'user_has_company_succursale_id', 'ressource_id')->withPivot(['create', 'read', 'update', 'delete', 'status'])->as('access');
    }

    public function item()
    {
        return $this->belongsTo(Succursales::class,'succursale_id','id');
    }
}
