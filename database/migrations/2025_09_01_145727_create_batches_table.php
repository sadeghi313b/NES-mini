<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('cut_id')->nullable(); //!changed/
            $table->unsignedSmallInteger('quantity')->nullable(); //!changed/
            $table->dateTime('printing_date')->nullable();

            $table->text('description')->nullable();
            $table->boolean('status')->nullable(); //!changed/
            $table->unsignedInteger('created_by')->nullable(); //!changed/
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
