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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');

            $table->string('name', 255);

            // Cable specifications
            $table->string('cable_name', 64);
            $table->unsignedSmallInteger('cable_length');
            $table->string('cable_color', 50);
            $table->unsignedTinyInteger('cable_tall_strip_length');
            $table->unsignedTinyInteger('cable_short_strip_length');

            // Wire cut lengths
            $table->unsignedTinyInteger('blue_wire_cut_length');
            $table->unsignedTinyInteger('brown_wire_cut_length');
            $table->unsignedTinyInteger('yellow_wire_cut_length');

            // Wire strip lengths
            $table->unsignedTinyInteger('blue_wire_strip_length');
            $table->unsignedTinyInteger('brown_wire_strip_length');
            $table->unsignedTinyInteger('yellow_wire_strip_length');

            // Foreign keys to applicators
            $table->foreignId('blue_wire_applicator_id')->constrained('applicators')->onDelete('cascade');
            $table->foreignId('brown_wire_applicator_id')->constrained('applicators')->onDelete('cascade');
            $table->foreignId('yellow_wire_applicator_id')->constrained('applicators')->onDelete('cascade');

            // Mold reference
            $table->foreignId('molds_id')->constrained('molds')->onDelete('cascade');

            // Cord specifications
            $table->unsignedTinyInteger('cord_length');

            // Double wire specifications
            $table->foreignId('double_wire_applicator_id')->constrained('applicators')->onDelete('cascade');
            $table->unsignedTinyInteger('double_wire_length');

            // Plug type
            $table->enum('plug_type', ['Ref', 'Tv', 'Triple']);

            // General fields
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['cable_name']);
            $table->index(['cable_color']);
            $table->index(['plug_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
