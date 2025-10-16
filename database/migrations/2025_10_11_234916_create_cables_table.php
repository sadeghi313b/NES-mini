<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cables', function (Blueprint $table) {
            $table->id();
            $table->string('name', 32)->nullable()->comment('searchable');
            $table->string('color', 32)->nullable()->comment('filterable');

            // Common fields for all tables
            $table->text('description')->nullable()->comment('searchable');
            $table->json('tags')->nullable()->comment('filterable'); // Stores tags as JSON array
            $table->unsignedInteger('created_by')->nullable();

            $table->timestamps(); // creates created_at and updated_at
            $table->softDeletes(); // creates deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cables');
    }
};
