<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('plugs', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->string('tag')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['type']);
            $table->index(['tag']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('plugs');
    }
};