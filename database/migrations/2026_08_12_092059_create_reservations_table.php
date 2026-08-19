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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('offer_variants')->onDelete('cascade');
            $table->string('customer');
            $table->string('phone');
            $table->integer('travellers_number');
            $table->string('wilaya');
            $table->string('status');
            $table->boolean('include_feeding')->default(false);
            $table->unsignedBigInteger('total_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
