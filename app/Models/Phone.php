<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Phone extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_by'];

    protected $appends = ['phone_no']; // Virtual attributes
    public function getPhoneNoAttribute()
    {
        if (empty($this->pre_country_number) || empty($this->pre_zone_number) || empty($this->number)) {
            return null;
        }
        return '(' . $this->pre_country_number . ')' . $this->pre_zone_number . '-' . $this->number;
    }

    protected $casts = [
        'is_main_for_sms' => 'boolean',
        'is_main_for_eitaa' => 'boolean',
        'status' => 'boolean',
        'created_at' => 'datetime:Y/m/d H:i',
        'updated_at' => 'datetime:Y/m/d H:i',
        'deleted_at' => 'datetime:Y/m/d H:i',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
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
