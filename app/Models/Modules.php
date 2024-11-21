<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modules extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['name', 'label', 'fonctionnalite'];

    public function ressource()
    {
        return $this->hasMany(Ressources::class, 'module_id', 'id');
    }
}
