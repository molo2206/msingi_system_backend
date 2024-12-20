<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abonnement extends Model
{
    use HasFactory, HasUuids;
    protected  $fillable = ['company_id', 'token', 'expires', 'plans_id', 'total_price','month'];
    protected $hidden = ['company_id','token'];

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function plan(){
        return $this->belongsTo(Plans::class,'plans_id');
    }
}
