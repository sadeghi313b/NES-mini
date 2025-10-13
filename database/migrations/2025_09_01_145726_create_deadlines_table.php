<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deadlines', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_id')->nullable(); 
            $table->unsignedMediumInteger('part_quantity')->nullable(); 
            $table->date('due_date')->nullable(); 

            $table->text('description')->nullable();
            $table->boolean('status')->nullable(); 
            $table->unsignedInteger('created_by')->nullable(); 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deadlines');
    }
};
