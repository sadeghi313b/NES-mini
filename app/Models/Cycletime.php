<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cycletime extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_by'];

    protected $casts = [
        'cycletime' => 'float',
        'status' => 'boolean',
        'created_at' => 'datetime:Y/m/d H:i',
        'updated_at' => 'datetime:Y/m/d H:i',
        'deleted_at' => 'datetime:Y/m/d H:i',
    ];

    // Relationships
    // public function product()
    // {
    //     return $this->belongsTo(Product::class);
    // }

    // public function activity()
    // {
    //     return $this->belongsTo(Activity::class);
    // }

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