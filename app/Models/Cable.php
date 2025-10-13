<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\GetFields;

class Cable extends Model
{
    use HasFactory, SoftDeletes;
    use GetFields;

    /* -------------------------------------------------------------------------- */
    /*                                definitions                                  */
    /* -------------------------------------------------------------------------- */
    const COLORS = ['red', 'gray', 'dark gray', 'black', 'white'];

    /* -------------------------------------------------------------------------- */
    /*                        fillables & hiddens & casts                          */
    /* -------------------------------------------------------------------------- */
    protected $guarded = ['id', 'created_by'];
    protected $hidden = [];
    protected $casts = [
        'tags' => 'array',
        'created_at' => 'datetime:Y/m/d H:i',
        'updated_at' => 'datetime:Y/m/d H:i',
        'deleted_at' => 'datetime:Y/m/d H:i',
    ];

    /* -------------------------------------------------------------------------- */
    /*                               Relationships                                 */
    /* -------------------------------------------------------------------------- */
    /* ------------------------------- created by ------------------------------- */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* -------------------------------------------------------------------------- */
    /*                                   boot                                      */
    /* -------------------------------------------------------------------------- */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->created_by && auth()->check()) {
                $model->created_by = auth()->id();
            }
        });
    }
}
