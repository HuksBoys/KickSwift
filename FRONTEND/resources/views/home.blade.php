@extends('layouts.app')

@section('title', 'KickSwift - Booking Lapangan Futsal & AI Match Assistant')

@section('content')
<div class="space-y-16 pb-20">

    <!-- Hero Section -->
    <section class="relative pt-12 pb-16 overflow-hidden">
        <!-- Background Glow FX -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[300px] bg-emerald-500/15 blur-[120px] pointer-events-none rounded-full"></div>
        <div class="absolute top-1/3 left-1/4 w-[300px] h-[300px] bg-teal-500/10 blur-[100px] pointer-events-none rounded-full"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-950/80 border border-emerald-500/30 text-emerald-400 text-xs font-semibold uppercase tracking-wider mb-6 shadow-inner">
                <i data-lucide="zap" class="w-3.5 h-3.5 fill-emerald-400"></i>
                <span>Platform Booking Lapangan Futsal Modern #1</span>
            </div>

            <h1 class="font-display font-black text-4xl sm:text-6xl lg:text-7xl tracking-tight text-white max-w-4xl mx-auto leading-tight sm:leading-[1.1]">
                Main Futsal Makin Seru, <br>
                <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-400 bg-clip-text text-transparent">
                    Booking Instan & Cerdas.
                </span>
            </h1>

            <p class="mt-6 text-base sm:text-lg text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Pilih lapangan favorit Anda (Vinyl Standar Internasional, Rumput Sintetis Premium, atau Interlocking), pantau jadwal kosong secara real-time, dan konsultasikan jadwal dengan <strong>AI Chatbot</strong>.
            </p>

            <!-- CTA Buttons -->
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                <a href="#katalog" class="px-7 py-3.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-sm shadow-xl shadow-emerald-500/25 hover:scale-105 transition-all duration-300 flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                    Lihat Ketersediaan Lapangan
                </a>
                <a href="{{ route('demo.customer') }}" class="px-6 py-3.5 rounded-2xl bg-slate-900/90 hover:bg-slate-800 text-slate-200 font-semibold text-sm border border-slate-700 hover:border-slate-600 transition flex items-center gap-2">
                    <i data-lucide="play" class="w-4 h-4 text-emerald-400"></i>
                    Coba Demo Customer
                </a>
            </div>

            <!-- Quick Stats -->
            <div class="mt-14 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto">
                <div class="p-4 rounded-2xl bg-[#0e1626]/80 border border-slate-800 backdrop-blur-sm">
                    <div class="font-display font-bold text-2xl text-emerald-400">3 Varian</div>
                    <div class="text-xs text-slate-400 mt-0.5">Vinyl, Rumput & Interlocking</div>
                </div>
                <div class="p-4 rounded-2xl bg-[#0e1626]/80 border border-slate-800 backdrop-blur-sm">
                    <div class="font-display font-bold text-2xl text-emerald-400">100% Real-Time</div>
                    <div class="text-xs text-slate-400 mt-0.5">Bebas Bentrok & Double Book</div>
                </div>
                <div class="p-4 rounded-2xl bg-[#0e1626]/80 border border-slate-800 backdrop-blur-sm">
                    <div class="font-display font-bold text-2xl text-emerald-400">AI Assistant</div>
                    <div class="text-xs text-slate-400 mt-0.5">Rekomendasi Cerdas 24/7</div>
                </div>
                <div class="p-4 rounded-2xl bg-[#0e1626]/80 border border-slate-800 backdrop-blur-sm">
                    <div class="font-display font-bold text-2xl text-emerald-400">Otomatis QRIS</div>
                    <div class="text-xs text-slate-400 mt-0.5">Konfirmasi Pembayaran Cepat</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Field Catalog Section -->
    <section id="katalog" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 scroll-mt-24">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-400">Pilihan Lapangan</span>
                <h2 class="font-display font-bold text-3xl sm:text-4xl text-white mt-1">
                    Katalog Lapangan KickSwift
                </h2>
            </div>

            <!-- Filter Pills -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('home') }}#katalog" class="px-4 py-2 rounded-xl text-xs font-bold {{ !$typeFilter ? 'bg-emerald-500 text-slate-950' : 'bg-slate-900 text-slate-300 hover:bg-slate-800' }} border border-slate-700 transition">
                    Semua Tipe
                </a>
                <a href="{{ route('home', ['type' => 'vinyl']) }}#katalog" class="px-4 py-2 rounded-xl text-xs font-bold {{ $typeFilter === 'vinyl' ? 'bg-emerald-500 text-slate-950' : 'bg-slate-900 text-slate-300 hover:bg-slate-800' }} border border-slate-700 transition">
                    Vinyl Pro
                </a>
                <a href="{{ route('home', ['type' => 'synthetic_grass']) }}#katalog" class="px-4 py-2 rounded-xl text-xs font-bold {{ $typeFilter === 'synthetic_grass' ? 'bg-emerald-500 text-slate-950' : 'bg-slate-900 text-slate-300 hover:bg-slate-800' }} border border-slate-700 transition">
                    Rumput Sintetis
                </a>
                <a href="{{ route('home', ['type' => 'interlocking']) }}#katalog" class="px-4 py-2 rounded-xl text-xs font-bold {{ $typeFilter === 'interlocking' ? 'bg-emerald-500 text-slate-950' : 'bg-slate-900 text-slate-300 hover:bg-slate-800' }} border border-slate-700 transition">
                    Interlocking
                </a>
            </div>
        </div>

        <!-- Field Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($fields as $field)
                <div class="group rounded-3xl bg-[#0e1626] border border-slate-800/80 hover:border-emerald-500/50 p-6 flex flex-col justify-between transition-all duration-300 glow-card">
                    <div>
                        <!-- Image Header Badge -->
                        <div class="relative h-48 rounded-2xl overflow-hidden mb-6 bg-gradient-to-br {{ $field->type === 'vinyl' ? 'from-blue-950 via-slate-900 to-emerald-950' : ($field->type === 'synthetic_grass' ? 'from-emerald-950 via-green-900 to-slate-950' : 'from-purple-950 via-slate-900 to-cyan-950') }} flex items-center justify-center border border-slate-700/50">
                            <!-- Surface Icon -->
                            <div class="text-center p-6">
                                <div class="w-14 h-14 mx-auto rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center mb-2 group-hover:scale-110 transition">
                                    @if($field->type === 'vinyl')
                                        <i data-lucide="layers" class="w-7 h-7 text-blue-400"></i>
                                    @elseif($field->type === 'synthetic_grass')
                                        <i data-lucide="trees" class="w-7 h-7 text-emerald-400"></i>
                                    @else
                                        <i data-lucide="grid" class="w-7 h-7 text-cyan-400"></i>
                                    @endif
                                </div>
                                <span class="text-xs font-bold tracking-wider text-slate-200 uppercase">
                                    {{ str_replace('_', ' ', $field->type) }}
                                </span>
                            </div>

                            <!-- Badge Status -->
                            <div class="absolute top-3 right-3 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-500/90 text-slate-950 shadow-md">
                                Buka (08:00 - 23:00)
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-md bg-slate-800 text-emerald-400 border border-slate-700">
                                @if($field->type === 'vinyl') Standard FIFA
                                @elseif($field->type === 'synthetic_grass') Premium Infill
                                @else Anti-Slip Pro
                                @endif
                            </span>
                        </div>

                        <h3 class="font-display font-bold text-xl text-white group-hover:text-emerald-400 transition-colors">
                            {{ $field->name }}
                        </h3>

                        <p class="mt-2 text-xs text-slate-400 leading-relaxed line-clamp-3">
                            {{ $field->description }}
                        </p>

                        <!-- Facilities List -->
                        <div class="mt-4 pt-4 border-t border-slate-800/80 flex flex-wrap gap-2 text-[11px] text-slate-400">
                            <span class="flex items-center gap-1"><i data-lucide="sun" class="w-3.5 h-3.5 text-amber-400"></i> Lampu LED</span>
                            <span class="flex items-center gap-1"><i data-lucide="wind" class="w-3.5 h-3.5 text-blue-400"></i> Ventilasi AC</span>
                            <span class="flex items-center gap-1"><i data-lucide="coffee" class="w-3.5 h-3.5 text-emerald-400"></i> Kantin & Toilet</span>
                        </div>
                    </div>

                    <!-- Pricing & CTA -->
                    <div class="mt-6 pt-5 border-t border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Tarif Mulai</span>
                            <div class="font-display font-extrabold text-lg text-emerald-400">
                                Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}
                                <span class="text-xs text-slate-400 font-normal">/jam</span>
                            </div>
                        </div>

                        <a href="{{ route('fields.show', $field->slug) }}" class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs flex items-center gap-1.5 shadow-lg shadow-emerald-500/20 group-hover:gap-2.5 transition-all">
                            <span>Pilih Jadwal</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-16 bg-[#0e1626] rounded-3xl border border-slate-800">
                    <i data-lucide="inbox" class="w-12 h-12 text-slate-500 mx-auto mb-3"></i>
                    <p class="text-slate-300 font-semibold text-sm">Tidak ada lapangan yang sesuai dengan filter.</p>
                    <a href="{{ route('home') }}" class="mt-3 inline-block text-xs text-emerald-400 hover:underline">Reset Filter</a>
                </div>
            @endforelse
        </div>
    </section>

    <!-- How It Works & Features -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        <div class="p-8 sm:p-12 rounded-3xl bg-gradient-to-br from-[#0e1626] via-[#09101c] to-[#0c1422] border border-slate-800 relative overflow-hidden">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-400">Alur Pemesanan</span>
                <h2 class="font-display font-bold text-2xl sm:text-3xl text-white mt-1">
                    Booking Lapangan Hanya dalam 3 Langkah
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative z-10">
                <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 font-display font-black text-lg flex items-center justify-center mb-4">
                        1
                    </div>
                    <h4 class="font-bold text-white text-base">Pilih Lapangan & Slot</h4>
                    <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                        Tentukan tipe lapangan yang Anda sukai, pilih tanggal, dan klik jam yang masih hijau/tersedia.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 font-display font-black text-lg flex items-center justify-center mb-4">
                        2
                    </div>
                    <h4 class="font-bold text-white text-base">Konsultasi AI (Opsional)</h4>
                    <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                        Gunakan tombol AI Chatbot di pojok kanan bawah untuk menanyakan rekomendasi jenis lapangan dan harga.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 font-display font-black text-lg flex items-center justify-center mb-4">
                        3
                    </div>
                    <h4 class="font-bold text-white text-base">Konfirmasi & QRIS</h4>
                    <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                        Dapatkan kode booking instan dan selesaikan pembayaran QRIS untuk mengunci jadwal tanding Anda.
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
