<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('account_number', 50)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('landmark')->nullable();
            $table->text('billing_address');
            $table->string('contact_number', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('coordinates', 100)->nullable();
            $table->text('remarks')->nullable();
            $table->string('area', 100)->nullable()->index();
            $table->string('external_url', 2048)->nullable();
            $table->string('image_url', 2048)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscribers');
    }
};
