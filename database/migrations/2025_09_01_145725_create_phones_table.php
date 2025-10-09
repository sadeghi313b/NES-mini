<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('pre_country_number', 10)->default('+98');
            $table->string('pre_zone_number', 4)->default('21');
            $table->string('number', 11);
            $table->enum('phone_type',['mobile','home','work|office','fax'])->nullable();
            $table->boolean('is_main_for_sms')->default(false);
            $table->boolean('is_main_for_eitaa')->default(false);
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['user_id']);
            $table->index(['number']);
            $table->index(['is_main_for_sms']);
            $table->index(['is_main_for_eitaa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phones');
    }
};