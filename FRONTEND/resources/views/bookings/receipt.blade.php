@extends('layouts.app')

@section('title', 'Tiket Reservasi #' . $booking->booking_code . ' - KickSwift')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Back to Catalog -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-emerald-400 transition font-medium">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Katalog
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold border border-slate-700 flex items-center gap-1.5 transition">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                Cetak Tiket
            </button>
            <a href="{{ route('bookings.history') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold border border-slate-700 flex items-center gap-1.5 transition">
                <i data-lucide="history" class="w-3.5 h-3.5"></i>
                Riwayat Saya
            </a>
        </div>
    </div>

    <!-- Ticket / Invoice Card -->
    <div class="rounded-3xl bg-[#0e1626] border border-slate-800 overflow-hidden shadow-2xl">
        
        <!-- Header Banner -->
        <div class="p-6 sm:p-8 bg-gradient-to-r {{ $booking->payment_status === 'paid' ? 'from-emerald-950 via-[#0e1626] to-[#0a121f]' : 'from-amber-950/80 via-[#0e1626] to-[#0a121f]' }} border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs uppercase font-bold tracking-widest text-slate-400">Bukti Pemesanan Lapangan</span>
                <div class="flex items-center gap-3 mt-1">
                    <h1 class="font-display font-black text-2xl sm:text-3xl text-white">
                        {{ $booking->booking_code }}
                    </h1>
                </div>
                <p class="text-xs text-slate-400 mt-1">Dibuat pada {{ $booking->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>

            <!-- Status Badges -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Booking Status -->
                <span class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold uppercase tracking-wider
                    {{ $booking->status === 'confirmed' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : '' }}
                    {{ $booking->status === 'pending' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : '' }}
                    {{ $booking->status === 'completed' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' : '' }}
                    {{ $booking->status === 'cancelled' ? 'bg-red-500/20 text-red-400 border border-red-500/30' : '' }}
                ">
                    Status: {{ ucfirst($booking->status) }}
                </span>

                <!-- Payment Status -->
                <span class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold uppercase tracking-wider
                    {{ $booking->payment_status === 'paid' ? 'bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/30' : 'bg-amber-500 text-slate-950' }}
                ">
                    {{ $booking->payment_status === 'paid' ? 'LUNAS / TERVERIFIKASI' : 'BELUM DIBAYAR' }}
                </span>
            </div>
        </div>

        <!-- Body Details -->
        <div class="p-6 sm:p-8 space-y-8">
            
            <!-- Reservation Info Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Lapangan</span>
                    <span class="font-bold text-white text-sm mt-0.5 block">{{ $booking->field->name }}</span>
                    <span class="text-[11px] text-emerald-400 font-semibold uppercase">{{ str_replace('_', ' ', $booking->field->type) }}</span>
                </div>

                <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Tanggal Main</span>
                    <span class="font-bold text-white text-sm mt-0.5 block">{{ $booking->booking_date->translatedFormat('l, d F Y') }}</span>
                </div>

                <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Jam Main</span>
                    <span class="font-bold text-emerald-400 text-sm mt-0.5 block">
                        {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }} WIB
                    </span>
                    <span class="text-[11px] text-slate-400">Durasi: {{ $booking->duration_hours }} Jam</span>
                </div>

                <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Pemesan</span>
                    <span class="font-bold text-white text-sm mt-0.5 block">{{ $booking->user->name }}</span>
                    <span class="text-[11px] text-slate-400">{{ $booking->user->phone ?? $booking->user->email }}</span>
                </div>
            </div>

            <!-- Payment Breakdown Table -->
            <div class="rounded-2xl bg-slate-900/50 border border-slate-800 p-6 space-y-3">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Rincian Pembayaran</h4>
                <div class="flex justify-between text-sm text-slate-300">
                    <span>Sewa {{ $booking->field->name }} ({{ $booking->duration_hours }} Jam)</span>
                    <span>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm text-slate-300">
                    <span>Metode Pembayaran</span>
                    <span class="font-semibold uppercase text-emerald-400">{{ $booking->payment_method ?? 'QRIS' }}</span>
                </div>
                <div class="pt-3 border-t border-slate-800 flex justify-between items-baseline">
                    <span class="font-display font-bold text-base text-white">Total Tagihan</span>
                    <span class="font-display font-black text-2xl text-emerald-400">
                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- QRIS / Instant Payment Section (If Unpaid) -->
            @if($booking->payment_status !== 'paid' && $booking->status !== 'cancelled')
                <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-[#101b2f] to-[#0c1422] border-2 border-emerald-500/40 text-center">
                    <span class="text-xs font-bold uppercase tracking-widest text-emerald-400 block mb-1">Simulasi Pembayaran Instan</span>
                    <h3 class="font-display font-bold text-xl text-white">Scan QRIS untuk Menyelesaikan Pembayaran</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                        Gunakan aplikasi GoPay, OVO, Dana, ShopeePay, BCA Mobile, atau Livin Mandiri untuk scan barcode di bawah.
                    </p>

                    <!-- Mock QRIS Code Graphic -->
                    <div class="my-6 inline-block p-4 rounded-2xl bg-white shadow-xl shadow-emerald-500/10">
                        <div class="w-44 h-44 bg-slate-900 rounded-xl p-2 flex flex-col items-center justify-center text-center">
                            <i data-lucide="qr-code" class="w-28 h-28 text-white stroke-[1.5]"></i>
                            <span class="text-[9px] font-bold text-emerald-400 uppercase tracking-widest mt-1">KickSwift QRIS PRO</span>
                        </div>
                    </div>

                    <!-- Instant Simulation Button -->
                    <div class="max-w-md mx-auto space-y-3">
                        <form action="{{ route('bookings.pay', $booking->booking_code) }}" method="POST">
                            @csrf
                            <input type="hidden" name="payment_method" value="qris">
                            <button type="submit" class="w-full py-3.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-display font-black text-sm shadow-xl shadow-emerald-500/25 transition-all flex items-center justify-center gap-2">
                                <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                                <span>Bayar Sekarang (Simulasi QRIS Sukses)</span>
                            </button>
                        </form>

                        <form action="{{ route('bookings.cancel', $booking->booking_code) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pemesanan ini?')">
                            @csrf
                            <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-red-950/40 text-slate-400 hover:text-red-400 text-xs font-semibold border border-slate-700 hover:border-red-800 transition">
                                Batalkan Reservasi
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <!-- Paid Confirmation Banner -->
                <div class="p-6 rounded-2xl bg-emerald-950/40 border border-emerald-500/40 text-center text-emerald-300">
                    <i data-lucide="check-circle" class="w-10 h-10 text-emerald-400 mx-auto mb-2"></i>
                    <h4 class="font-bold text-base text-white">Pembayaran Telah Dikonfirmasi!</h4>
                    <p class="text-xs text-slate-300 mt-1">
                        Tunjukkan kode reservasi <strong>{{ $booking->booking_code }}</strong> kepada kasir lapangan saat datang bertanding. Selamat bermain!
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
