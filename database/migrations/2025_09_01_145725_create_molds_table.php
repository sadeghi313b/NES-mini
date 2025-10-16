<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('molds', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->enum('category', ['plug', 'cord'])->nullable();
            $table->unsignedTinyInteger('number_of_cavity')->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true)->nullable();
            $table->unsignedSmallInteger('created_by')->nullable(); 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('molds');
    }
};
