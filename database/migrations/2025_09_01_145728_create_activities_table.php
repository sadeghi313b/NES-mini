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
                $table->string('name', 255);
                // $table->string('interchangable_category', 255)->nullable();

                $table->text('description')->nullable();
                $table->boolean('status')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                $table->softDeletes();

                // Indexes
                $table->index(['name']);
                // $table->index(['interchangable_category']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
