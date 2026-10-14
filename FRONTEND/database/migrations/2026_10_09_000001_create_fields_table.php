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
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "Lapangan Vinyl A"
            $table->string('slug')->unique(); // e.g. "lapangan-vinyl-a"
            $table->enum('type', ['vinyl', 'synthetic_grass', 'interlocking', 'cement'])->default('vinyl');
            $table->text('description')->nullable();
            $table->decimal('price_per_hour', 12, 2); // Tarif per jam dalam IDR
            $table->string('image')->nullable(); // Path foto lapangan
            $table->boolean('is_active')->default(true); // Status operasional lapangan
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
