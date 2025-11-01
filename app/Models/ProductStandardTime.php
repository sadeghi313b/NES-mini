<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductStandardTime extends Model
{
    use HasFactory, SoftDeletes;

    //. -------------------------------------------------------------------------- */
    //.                                Configuration                               */
    //. -------------------------------------------------------------------------- */
    protected $table = 'product_standard_times';

    protected $fillable = [
        'product_id',
        'activity_id',
        'stanndard_time', 
        'description',
        'status',
        'tags',
    ];

    //. -------------------------------------------------------------------------- */
    //.                                Casts                                       */
    //. -------------------------------------------------------------------------- */
    protected $casts = [
        'tags' => 'array',                  // Store JSON as array
        'status' => 'boolean',              // Convert tinyint to boolean
        'stanndard_time' => 'float',      // Ensure numeric cast
        'product_id' => 'integer',
        'activity_id' => 'integer',
        'created_by' => 'integer',
    ];

    //. -------------------------------------------------------------------------- */
    //.                                Relationships                               */
    //. -------------------------------------------------------------------------- */

    //# product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    //# activity
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    //# creator
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
