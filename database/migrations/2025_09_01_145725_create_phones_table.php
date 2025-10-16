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
            $table->unsignedInteger('user_id')->nullable();
            $table->string('pre_country_number', 10)->default('+98')->nullable();
            $table->string('pre_zone_number', 4)->default('21')->nullable();
            $table->string('number', 11)->nullable();
            $table->enum('phone_type', ['mobile', 'home', 'work|office', 'fax'])->nullable();
            $table->boolean('is_main_for_sms')->default(false)->nullable();
            $table->boolean('is_main_for_eitaa')->default(false)->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true)->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phones');
    }
};
