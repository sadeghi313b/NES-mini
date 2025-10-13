<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_id')->nullable(); //!changed/
            $table->unsignedMediumInteger('quantity')->nullable(); //!changed/
            $table->unsignedMediumInteger('maximum_batch_size')->nullable(); //!changed/
            $table->dateTime('printing_date')->nullable();
            $table->date('cutting_date')->nullable(); //!changed/

            $table->text('description')->nullable();
            $table->boolean('status')->nullable(); //!changed/
            $table->unsignedInteger('created_by')->nullable(); //!changed/
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuts');
    }
};
