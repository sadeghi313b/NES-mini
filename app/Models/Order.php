<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    /* -------------------------------- Accessors ------------------------------- */
    protected function seenLabel(): Attribute //seenInfo
    {
        return Attribute::make(
            get: fn() => $this->seen ? 'seen' : 'unseen',
            set: fn($value) => $value === 'seen'
        );
    }

    /* ------------------------ filterables & searcjables ----------------------- */
    // فیلدهایی که قابلیت جستجو دارند
    // $order->searchable
    protected $searchable = [
        'product',
        'description',
    ];

    // فیلدهایی که قابل فیلتر هستند
    // 
    protected $filterable = [
        'product',
        'month',
        'status',
        'seen',
    ];

    // روابطی که می‌توان روی آن‌ها جستجو کرد
    // $order->searchableRelations
    protected $searchableRelations = [
        'product' => 'product_id',
    ];

    /* ------------------------------ Relationships ----------------------------- */
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
