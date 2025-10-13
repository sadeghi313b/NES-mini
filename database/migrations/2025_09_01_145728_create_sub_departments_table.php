<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('department_id')->nullable(); //!changed/
            $table->string('name', 255)->nullable(); //!changed/

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
        Schema::dropIfExists('sub_departments');
    }
};
