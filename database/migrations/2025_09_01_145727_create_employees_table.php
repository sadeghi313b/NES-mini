<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('sub_department_id')->constrained('sub_departments')->onDelete('cascade');
            $table->unsignedSmallInteger('employee_number')->unique();
            $table->date('hire_date');
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['user_id']);
            $table->index(['department_id']);
            $table->index(['sub_department_id']);
            $table->index(['employee_number']);
            $table->index(['hire_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};