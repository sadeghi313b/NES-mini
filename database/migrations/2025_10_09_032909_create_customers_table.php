<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // not null
            $table->text('description')->nullable();
            $table->string('tags')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['user_id']);
            $table->index(['name']);
            $table->index(['tags']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};