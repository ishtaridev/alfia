<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_variant_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('collectif_room')->default(0);
            $table->unsignedBigInteger('room_of_four')->default(0);
            $table->unsignedBigInteger('room_of_three')->default(0);
            $table->unsignedBigInteger('room_of_two')->default(0);
            $table->unsignedBigInteger('feeding')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_pricings');
    }
};
