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
            $table->string('name', 255);
            $table->enum('category', ['plug', 'cord']);
            $table->unsignedTinyInteger('number_of_cavity');
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['category']);
            $table->index(['number_of_cavity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('molds');
    }
};