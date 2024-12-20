<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ressources extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'module_id',
        'label',
        'fonctionnalite'
    ];

    public function module(){
         return $this->belongsTo(Modules::class, 'module_id', 'id');
    }
}
