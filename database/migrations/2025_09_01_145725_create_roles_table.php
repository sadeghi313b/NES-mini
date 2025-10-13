<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true)->nullable();
            $table->unsignedInteger('created_by')->nullable(); 
            $table->timestamps()->nullable();
            $table->softDeletes()->nullable();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable(); 
            $table->unsignedInteger('role_id')->nullable();
            $table->unsignedInteger('assigned_by')->nullable(); 
            $table->timestamp('assigned_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
