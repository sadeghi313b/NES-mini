<?php
namespace App\Traits;

use Morilog\Jalali\Jalalian;
use Morilog\Jalali\CalendarUtils;

trait CommonJalaliDates
{
    /**
     * Accessor for created_at
     */
    public function getCreatedAtAttribute($value)
    {
        return $value
            ? Jalalian::fromDateTime($value)->format('Y/m/d')
            : null;
    }

    /**
     * Mutator for created_at
     */
    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = $value
            ? CalendarUtils::createCarbonFromFormat('Y/m/d', $value)->toDateTimeString()
            : null;
    }

    /**
     * Accessor for updated_at
     */
    public function getUpdatedAtAttribute($value)
    {
        return $value
            ? Jalalian::fromDateTime($value)->format('Y/m/d')
            : null;
    }

    /**
     * Mutator for updated_at
     */
    public function setUpdatedAtAttribute($value)
    {
        $this->attributes['updated_at'] = $value
            ? CalendarUtils::createCarbonFromFormat('Y/m/d', $value)->toDateTimeString()
            : null;
    }

    /**
     * Accessor for deleted_at
     */
    public function getDeletedAtAttribute($value)
    {
        return $value
            ? Jalalian::fromDateTime($value)->format('Y/m/d')
            : null;
    }

    /**
     * Mutator for deleted_at
     */
    public function setDeletedAtAttribute($value)
    {
        $this->attributes['deleted_at'] = $value
            ? CalendarUtils::createCarbonFromFormat('Y/m/d', $value)->toDateTimeString()
            : null;
    }
}
