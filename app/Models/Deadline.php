<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Morilog\Jalali\CalendarUtils;
use Morilog\Jalali\Jalalian;

class Deadline extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_by'];

    protected $casts = [
        'part_quantity' => 'integer',
        'due_date' => 'date:Y/m/d',
        'status' => 'boolean',
        'created_at' => 'datetime:Y/m/d H:i',
        'updated_at' => 'datetime:Y/m/d H:i',
        'deleted_at' => 'datetime:Y/m/d H:i',
    ];

    /* -------------------------------------------------------------------------- */
    /*                                  accessors                                 */
    /* -------------------------------------------------------------------------- */
    public function getPromisedDateAttribute($value)
    {
        return $value
            ? Jalalian::fromDateTime($value)->format('Y/m/d')
            : null;
    }
    public function setPromisedDateAttribute($value)
    {
        $this->attributes['promised_date'] = $value
            ? CalendarUtils::createCarbonFromFormat('Y/m/d', $value)->toDateString()  
            : null;  
    }

    /* -------------------------------------------------------------------------- */
    /*                                Relationships                               */
    /* -------------------------------------------------------------------------- */
    public function order()
    {
        return $this->belongsTo(Order::class);
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