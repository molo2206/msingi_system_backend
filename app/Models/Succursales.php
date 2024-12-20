<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Succursales extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['company_id', 'name', 'adresse', 'email', 'phone', 'lat', 'long'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
