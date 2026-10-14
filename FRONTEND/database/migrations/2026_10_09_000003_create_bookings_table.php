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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique(); // Format: KS-YYYYMMDD-XXXX
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('field_id')->constrained('fields')->cascadeOnDelete();
            $table->date('booking_date'); // Tanggal pemakaian lapangan
            $table->time('start_time'); // Jam mulai main
            $table->time('end_time'); // Jam selesai main
            $table->unsignedInteger('duration_hours')->default(1); // Durasi main (jam)
            $table->decimal('price_per_hour', 12, 2); // Harga per jam saat booking dibuat
            $table->decimal('total_price', 12, 2); // Total biaya sewa
            
            // Status Pemesanan
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            
            // Status Pembayaran & Integrasi Payment Gateway (Midtrans/QRIS)
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded', 'failed'])->default('unpaid');
            $table->string('payment_method')->nullable(); // 'midtrans', 'qris', 'bank_transfer', 'cash'
            $table->string('payment_reference')->nullable(); // Snap Token / Order ID gateway
            $table->timestamp('paid_at')->nullable();
            
            $table->text('notes')->nullable(); // Catatan tambahan pemesan
            $table->timestamps();
            $table->softDeletes();

            // Indeks untuk pencarian dan pengecekan jadwal terisi
            $table->index(['field_id', 'booking_date', 'status']);
            $table->index(['user_id', 'booking_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
