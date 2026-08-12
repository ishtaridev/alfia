<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $table->date('travel_date');
            $table->string('airport');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_variants');
    }
};
