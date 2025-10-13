<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id')->nullable(); //!changed/
            $table->date('date')->nullable(); //!changed/
            $table->unsignedMediumInteger('quantity')->nullable(); //!changed/

            $table->text('description')->nullable();
            $table->boolean('status')->nullable(); //!changed/
            $table->unsignedInteger('created_by')->nullable(); //!changed/
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('delivery_order', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('delivery_id')->nullable(); //!changed/
            $table->unsignedInteger('order_id')->nullable(); //!changed/
            $table->unsignedMediumInteger('quantity')->nullable(); //!changed/

            $table->text('description')->nullable();
            $table->boolean('status')->nullable(); //!changed/
            $table->unsignedInteger('created_by')->nullable(); //!changed/
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
