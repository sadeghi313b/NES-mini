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
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->unsignedMediumInteger('quantity');
            $table->unsignedMediumInteger('maximum_batch_size');
            $table->dateTime('printing_date')->nullable();
            $table->date('cutting_date');
            
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['order_id']);
            $table->index(['cutting_date']);
            $table->index(['quantity']);
            $table->index(['maximum_batch_size']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuts');
    }
};