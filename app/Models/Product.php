<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * Model for products.
 *
 * This model includes technical specifications for cable products,
 * relationships with customers, applicators, molds, and production data.
 */
class Product extends Model
{
    use HasFactory, SoftDeletes;

    // Table name (optional if following Laravel convention)
    protected $table = 'products';

    // Primary key (optional if using 'id')
    protected $primaryKey = 'id';

    /**
     * Attributes that are NOT mass assignable.
     *
     * Using guarded to allow all fields except 'id' for easier management
     * due to the high number of fields in this model.
     */
    protected $guarded = ['id','created_by'];

    /**
     * Cast attributes to specific types.
     *
     * Important: Format dates and datetimes as specified.
     */
    protected $casts = [
        // Integer fields
        'cable_length'                => 'integer',
        'cable_tall_strip_length'     => 'integer',
        'cable_short_strip_length'    => 'integer',
        'blue_wire_cut_length'        => 'integer',
        'brown_wire_cut_length'       => 'integer',
        'yellow_wire_cut_length'      => 'integer',
        'blue_wire_strip_length'      => 'integer',
        'brown_wire_strip_length'     => 'integer',
        'yellow_wire_strip_length'    => 'integer',
        'cord_length'                 => 'integer',
        'double_wire_length'          => 'integer',

        // String enum
        'plug_type'                   => 'string',

        // Boolean (general field)
        'status'                      => 'boolean',

        // Date and datetime fields with custom format
        'created_at'                  => 'datetime:Y/m/d H:i',  // Format: 1403/06/15 14:30
        'updated_at'                  => 'datetime:Y/m/d H:i',
        'deleted_at'                  => 'datetime:Y/m/d H:i',
    ];

    //. -------------------------------------------------------------------------- */
    //.                                Relationships                               */
    //. -------------------------------------------------------------------------- */

    /* -------------------------------- customer -------------------------------- */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /* ------------------------------- terminals -------------------------------- */
    public function blueTerminal()
    {
        return $this->belongsTo(Terminal::class, 'blue_terminal_id', 'id');
    }

    public function brownTerminal()
    {
        return $this->belongsTo(Terminal::class, 'brown_terminal_id', 'id');
    }

    public function yellowTerminal()
    {
        return $this->belongsTo(Terminal::class, 'yellow_terminal_id', 'id');
    }

    public function doubleTerminal()
    {
        return $this->belongsTo(Terminal::class, 'double_terminal_id', 'id');
    }

    /* ------------------------------- applicators ------------------------------ */
    public function doubleWireApplicator()
    {
        return $this->belongsTo(Applicator::class, 'double_wire_applicator_id', 'id');
    }

    /* ---------------------------------- molds --------------------------------- */
    public function mold()
    {
        return $this->belongsTo(Mold::class, 'molds_id', 'id');
    }

    /* --------------------------------- orders --------------------------------- */
    public function orders()
    {
        return $this->hasMany(Order::class, 'product_id', 'id');
    }

    /* ------------------------------- cycleTimes ------------------------------- */
    public function cycleTimes()
    {
        return $this->hasMany(Cycletime::class, 'product_id', 'id');
    }

    /* ------------------------------- deliveries ------------------------------- */
    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'product_id', 'id');
    }

    /* -------------------------------- createdBy ------------------------------- */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    //. -------------------------------------------------------------------------- */
    //.                      Boot Method - Auto Set created_by                     */
    //. -------------------------------------------------------------------------- */

    /**
     * Boot the model and attach event listeners.
     *
     * Automatically set the 'created_by' field to the authenticated user's ID
     * when creating a new product.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            // Only set if not already set and user is logged in
            if (! $product->created_by && Auth::check()) {
                $product->created_by = Auth::id(); // Set current user ID
            }
        });
    }
}
