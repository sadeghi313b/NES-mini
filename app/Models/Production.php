<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Morilog\Jalali\CalendarUtils;
use Morilog\Jalali\Jalalian;

class Production extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Table name.
     */
    protected $table = 'productions';

    //. -------------------------------------------------------------------------- */
    //.                               Mass assignable                              */
    //. -------------------------------------------------------------------------- */
    protected $fillable = [
        'date',
        'product_id',
        'quantity',
        'description',
        'status',
        'tags',
        'created_by',
    ];

    //. -------------------------------------------------------------------------- */
    //.                                    casts                                   */
    //. -------------------------------------------------------------------------- */
    protected $casts = [
        'date' => 'datetime',
        'status' => 'boolean',
        'tags' => 'array',
    ];

    //. -------------------------------------------------------------------------- */
    //.                                  accessor                                  */
    //. -------------------------------------------------------------------------- */

    /* ----------------------------- date accessors ----------------------------- */
    public function getDateAttribute($value)
    {
        return $value
            ? Jalalian::fromDateTime($value)->format('Y/m/d')
            : null;
    }
    public function setDateAttribute($value)
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

    //# Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    //# creator
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //# orders
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_production')
            ->withPivot('id', 'quantity'); // include pivot fields

    }
}
