<?php
namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use Schema;

class BaseModel extends Model
{
    public static function getFields()
    {
        return Schema::getColumnListing((new static)->getTable());
    }
}