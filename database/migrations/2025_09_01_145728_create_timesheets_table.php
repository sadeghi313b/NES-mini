<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('employee_id')->nullable(); //!changed/
            $table->unsignedInteger('batch_id')->nullable(); //!changed/
            $table->unsignedInteger('activity_id')->nullable(); //!changed/
            $table->unsignedMediumInteger('quantity')->nullable(); //!changed/
            $table->date('date')->nullable(); //!changed/

            $table->text('description')->nullable();
            $table->boolean('status')->nullable(); //!changed/
            $table->unsignedInteger('created_by')->nullable(); //!changed/
            $table->timestamps();
            $table->softDeletes();

            // Indexes removed
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timesheets');
    }
};
