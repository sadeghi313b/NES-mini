<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Morilog\Jalali\CalendarUtils;
use Morilog\Jalali\Jalalian;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id', 'created_by'];

    protected $casts = [
        'quantity' => 'integer',
        'notification_date' => 'date:Y/m/d',
        'seen' => 'boolean',
        'status' => OrderStatus::class,
        'created_at' => 'datetime:Y/m/d H:i',
        'updated_at' => 'datetime:Y/m/d H:i',
        'deleted_at' => 'datetime:Y/m/d H:i',
    ];

    // protected $jalaliDates = [
    //     'notification_date',
    //     'created_at', 'updated_at', 'deleted_at',
    // ];

    //. -------------------------------------------------------------------------- */
    //.                                  accessor                                  */
    //. -------------------------------------------------------------------------- */
    protected function seenLabel(): Attribute //seenInfo
    {
        return Attribute::make(
            get: fn() => $this->seen ? 'seen' : 'unseen',
            set: fn($value) => $value === 'seen'
        );
    }

    /* ----------------------------- date accessors ----------------------------- */
    public function getNotificationDateAttribute($value)
    {
        return $value
            ? Jalalian::fromDateTime($value)->format('Y/m/d')
            : null;
    }
    public function setNotificationDateAttribute($value)
    {
        if ($value) {
            [$jYear, $jMonth, $jDay] = explode('/', $value);
            [$gYear, $gMonth, $gDay] = CalendarUtils::toGregorian($jYear, $jMonth, $jDay);
            $this->attributes['notification_date'] = sprintf('%04d-%02d-%02d', $gYear, $gMonth, $gDay);
        } else {
            $this->attributes['notification_date'] = null;
        }
    }



    //. -------------------------------------------------------------------------- */
    //.                                Relationships                               */
    //. -------------------------------------------------------------------------- */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function month()
    {
        return $this->belongsTo(Month::class);
    }

    public function deadlines()
    {
        return $this->hasMany(Deadline::class);
    }

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    public function cuts()
    {
        return $this->hasMany(Cut::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //# productions
    public function productions()
    {
        return $this->belongsToMany(Production::class, 'order_production');
    }

    //. -------------------------------------------------------------------------- */
    //.                                    boot                                    */
    //. -------------------------------------------------------------------------- */
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
