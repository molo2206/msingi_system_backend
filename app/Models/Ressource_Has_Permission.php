<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ressource_Has_Permission extends Model
{
    use HasFactory;
    protected $fillable = ['user_has_company_id', 'ressource_id',
    'create','update','delete','status','read'];
}
