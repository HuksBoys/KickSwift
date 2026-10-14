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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained('fields')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week')->nullable(); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday (untuk jadwal berulang)
            $table->date('date')->nullable(); // Tanggal spesifik (untuk jadwal khusus / hari libur)
            $table->time('start_time'); // Jam mulai slot, misal '08:00:00'
            $table->time('end_time'); // Jam selesai slot, misal '09:00:00'
            $table->decimal('custom_price', 12, 2)->nullable(); // Override harga (peak hours / weekend)
            $table->boolean('is_available')->default(true); // Status apakah slot aktif & bisa di-booking
            $table->timestamps();

            // Indeks untuk pencarian cepat ketersediaan slot lapangan
            $table->index(['field_id', 'day_of_week', 'start_time']);
            $table->index(['field_id', 'date', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
