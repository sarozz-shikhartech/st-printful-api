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
        Schema::create('order_shipment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_log_id')->constrained('order_logs')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('shipment_id')->index();
            $table->string('status');
            $table->string('tracking_number');
            $table->string('ship_date');
            $table->string('ship_at');
            $table->boolean('reshipment')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_shipment_logs');
    }
};
