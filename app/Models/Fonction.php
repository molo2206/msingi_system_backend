<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fonction extends Model
{
    use HasFactory,HasUuids;
    protected $fillable = ['name','departement_id'];

    public function users(){
        return $this->belongsToMany(User::class,
        User_has_company::class)->withPivot('company_id');
    }

    public function company(){
        return $this->belongsToMany(Company::class,
        User_has_company::class)->withPivot('user_id');
    }

    public function departement(){
        return $this->belongsTo(Departement::class);
    }
}
