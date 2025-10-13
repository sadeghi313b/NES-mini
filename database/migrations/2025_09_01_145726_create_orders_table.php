<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id')->nullable(); 
            $table->unsignedInteger('month_id')->nullable(); 
            $table->unsignedMediumInteger('quantity')->nullable(); 
            $table->date('notification_date')->nullable(); 
            $table->boolean('seen')->nullable(); 
            $table->enum('status', ['active', 'force', 'hold', 'canceled', 'enough'])->nullable(); 

            $table->text('description')->nullable();
            $table->unsignedInteger('created_by')->nullable(); 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};



/*
id
order_number
user_id
status
subtotal
tax_amount
shipping_cost
discount_amount
total_amount
currency
payment_method
payment_status
paid_at
shipped_at
delivered_at
billing_name
billing_line1
billing_line2
billing_city
billing_postcode
billing_country
shipping_name
shipping_line1
shipping_line2
shipping_city
shipping_postcode
shipping_country
items_count
notes
coupon_code
created_by
created_at
updated_at
deleted_at

*/