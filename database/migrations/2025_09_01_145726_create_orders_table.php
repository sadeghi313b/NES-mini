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
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('month_id')->constrained('months')->onDelete('cascade');
            $table->unsignedMediumInteger('quantity');
            $table->date('notification_date');
            $table->boolean('seen')->default(false);
            $table->enum('status', ['active', 'force', 'hold', 'canceled', 'enough'])->default('active');

            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['product_id']);
            $table->index(['month_id']);
            $table->index(['notification_date']);
            $table->index(['status']);
            $table->index(['seen']);
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