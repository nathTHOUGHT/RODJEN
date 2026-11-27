<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('network_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('username')->nullable()->unique();
            $table->text('password')->nullable();
            $table->string('profile')->nullable();
            $table->text('comment')->nullable();
            $table->string('olt_device')->nullable();
            $table->string('pon_port')->nullable();
            $table->string('nap_device')->nullable();
            $table->string('nap_port')->nullable();
            $table->string('onu_name')->nullable();
            $table->string('caller_id')->nullable();
            $table->string('mac_address', 17)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('network_accounts');
    }
};
