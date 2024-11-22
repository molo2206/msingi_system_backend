<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plans extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['name', 'icon', 'monthly_price', 'yearly_price'];
    public function modules()
    {
        return $this->belongsToMany(Modules::class, 'plan_has_modules', 'plan_id', 'module_id')->withPivot(['plan_id', 'module_id']);
    }
}
