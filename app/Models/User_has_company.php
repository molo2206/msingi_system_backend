<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_has_company extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'company_id', 'fonction_id'];


    public function users(){
        return $this->belongsTo(User::class,'id', 'user_id');
    }

    public function company(){
        return $this->belongsTo(Company::class,'id', 'company_id');
    }

    public function fonction(){
        return $this->belongsTo(Fonction::class,'fonction_id', 'id');
    }

    public function permission()
    {
        return $this->belongsToMany(Ressources::class, 'ressource__has__permissions', 'user_has_company_id', 'ressource_id')->withPivot(['create', 'read', 'update', 'delete', 'status'])->as('access');
    }

}
