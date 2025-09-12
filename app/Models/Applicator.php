<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Applicator extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id','created_by'];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime:Y/m/d H:i',
        'updated_at' => 'datetime:Y/m/d H:i',
        'deleted_at' => 'datetime:Y/m/d H:i',
    ];

    // Relationships
    public function productsAsBlue()
    {
        return $this->hasMany(Product::class, 'blue_wire_applicator_id');
    }

    public function productsAsBrown()
    {
        return $this->hasMany(Product::class, 'brown_wire_applicator_id');
    }

    public function productsAsYellow()
    {
        return $this->hasMany(Product::class, 'yellow_wire_applicator_id');
    }

    public function productsAsDouble()
    {
        return $this->hasMany(Product::class, 'double_wire_applicator_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->created_by && auth()->check()) {
                $model->created_by = auth()->id();
            }
        });
    }
}