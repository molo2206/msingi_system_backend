<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modules extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['name', 'icon'];

    public function ressource()
    {
        return $this->hasMany(Ressources::class, 'module_id', 'id');
    }

    public function plans()
    {
        return $this->belongsToMany(
            Plans::class,
            'plan_has_modules',
            'module_id',
            'plan_id'
        );
    }
}
