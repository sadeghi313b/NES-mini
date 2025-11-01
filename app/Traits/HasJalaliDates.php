<?php
namespace App\Traits;

use Morilog\Jalali\Jalalian;
use Morilog\Jalali\CalendarUtils;

trait HasJalaliDates
{
    /**
     * Accessor: Convert Gregorian → Jalali
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->jalaliDates) && $value !== null) {
            return Jalalian::fromDateTime($value)->format('Y/m/d');
        }

        return $value;
    }

    /**
     * Mutator: Convert Jalali → Gregorian
     */
    public function setAttribute($key, $value)
    {
        if (property_exists($this, 'jalaliDates') && in_array($key, $this->jalaliDates) && $value !== null) {
            $value = CalendarUtils::createCarbonFromFormat('Y/m/d', $value)->toDateString();
        }

        parent::setAttribute($key, $value);
    }
}
