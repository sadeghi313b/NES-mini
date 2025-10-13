<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('activities')) {
            Schema::create('activities', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255)->nullable(); //!changed/
                // $table->string('interchangable_category', 255)->nullable();

                $table->text('description')->nullable();
                $table->boolean('status')->nullable(); //!changed/
                $table->unsignedInteger('created_by')->nullable(); //!changed/
                $table->timestamps();
                $table->softDeletes();

                // Indexes removed
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
