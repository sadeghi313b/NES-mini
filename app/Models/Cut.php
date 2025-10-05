<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cut extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_by'];

    protected $casts = [
        'quantity' => 'integer',
        'maximum_batch_size' => 'integer',
        'printing_date' => 'datetime:Y/m/d H:i',
        'cutting_date' => 'date:Y/m/d',
        'status' => 'boolean',
        'created_at' => 'datetime:Y/m/d H:i',
        'updated_at' => 'datetime:Y/m/d H:i',
        'deleted_at' => 'datetime:Y/m/d H:i',
    ];

    /* -------------------------------------------------------------------------- */
    /*                                Relationships                               */
    /* -------------------------------------------------------------------------- */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    /* -------------------------------------------------------------------------- */
    /*                                    boot                                    */
    /* -------------------------------------------------------------------------- */
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