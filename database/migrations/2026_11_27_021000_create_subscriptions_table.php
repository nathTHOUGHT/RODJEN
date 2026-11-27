<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscriber_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->date('installation_date')->nullable();
            $table->unsignedTinyInteger('billing_day')->nullable();
            $table->date('subscription_date')->nullable();
            $table->decimal('credit_limit', 10, 2)->default(0);
            $table->string('service_type', 32)->default('fiber');
            $table->string('status', 32)->default('active')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
