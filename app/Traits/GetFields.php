<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;

trait GetFields
{
    public static function getFieldsOfTable()
    {
        return Schema::getColumnListing((new static)->getTable());
    }
        public function getFieldsOfInstance()
    {
        return self::getFieldsOfTable(); // متد استاتیک را صدا می‌زنیم
    }
}
