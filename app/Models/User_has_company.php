<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_has_company extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'company_id', 'fonction_id'];

    // public function item_succursale()
    // {
    //     return $this->belongsTo(UserHasCompanyHasSuccursales::class, 'hasuser_id', 'id');
    // }

    public function item()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function succursales()
    {
        return $this->hasMany(UserHasCompanyHasSuccursales::class, 'hasuser_id', 'id')
            ->where('deleted', 0);
    }
    public function users()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'id', 'company_id');
    }

    public function fonction()
    {
        return $this->belongsTo(Fonction::class, 'fonction_id', 'id');
    }
}
