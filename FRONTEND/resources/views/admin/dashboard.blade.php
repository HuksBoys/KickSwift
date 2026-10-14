@extends('layouts.app')

@section('title', 'Admin Dashboard - KickSwift Futsal Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-950/80 border border-amber-500/30 text-amber-400 text-xs font-semibold mb-2">
                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                Panel Manajemen Pengelola Futsal
            </div>
            <h1 class="font-display font-black text-3xl text-white">Dashboard Operasional KickSwift</h1>
        </div>

        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-400">Login sebagai: <strong class="text-white">{{ Auth::user()->name }}</strong> (Admin)</span>
            <a href="{{ route('home') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition flex items-center gap-1.5">
                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                Lihat Web Depan
            </a>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-[#0e1626] border border-slate-800">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase">Total Omset Lunas</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <i data-lucide="banknote" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="font-display font-extrabold text-2xl text-emerald-400 mt-2">
                Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
            </div>
            <span class="text-[11px] text-slate-500 mt-1 block">Hari ini: Rp {{ number_format($stats['today_revenue'], 0, ',', '.') }}</span>
        </div>

        <div class="p-5 rounded-2xl bg-[#0e1626] border border-slate-800">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase">Booking Hari Ini</span>
                <div class="w-8 h-8 rounded-xl bg-blue-500/20 flex items-center justify-center text-blue-400">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="font-display font-extrabold text-2xl text-blue-400 mt-2">
                {{ $stats['today_bookings'] }} Slot
            </div>
            <span class="text-[11px] text-slate-500 mt-1 block">Total seumur hidup: {{ $stats['total_bookings'] }}</span>
        </div>

        <div class="p-5 rounded-2xl bg-[#0e1626] border border-slate-800">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase">Menunggu Pembayaran</span>
                <div class="w-8 h-8 rounded-xl bg-amber-500/20 flex items-center justify-center text-amber-400">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="font-display font-extrabold text-2xl text-amber-400 mt-2">
                {{ $stats['pending_bookings'] }} Pending
            </div>
            <span class="text-[11px] text-slate-500 mt-1 block">Perlu verifikasi/follow-up</span>
        </div>

        <div class="p-5 rounded-2xl bg-[#0e1626] border border-slate-800">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase">Pelanggan Terdaftar</span>
                <div class="w-8 h-8 rounded-xl bg-purple-500/20 flex items-center justify-center text-purple-400">
                    <i data-lucide="users" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="font-display font-extrabold text-2xl text-purple-400 mt-2">
                {{ $stats['total_customers'] }} User
            </div>
            <span class="text-[11px] text-slate-500 mt-1 block">Aktif di platform</span>
        </div>
    </div>

    <!-- Booking Table & Filter Filter Card -->
    <div class="rounded-3xl bg-[#0e1626] border border-slate-800 overflow-hidden shadow-2xl">
        
        <!-- Filters Bar -->
        <div class="p-5 bg-slate-900/80 border-b border-slate-800">
            <form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-wrap items-center gap-3">
                
                <!-- Date Filter -->
                <div>
                    <label class="text-[11px] text-slate-400 block mb-1">Filter Tanggal:</label>
                    <input 
                        type="date" 
                        name="date" 
                        value="{{ $dateFilter }}" 
                        onchange="this.form.submit()"
                        class="bg-slate-950 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 focus:outline-none focus:border-emerald-500 font-semibold"
                    >
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="text-[11px] text-slate-400 block mb-1">Status Booking:</label>
                    <select 
                        name="status" 
                        onchange="this.form.submit()"
                        class="bg-slate-950 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 focus:outline-none focus:border-emerald-500 font-semibold"
                    >
                        <option value="">Semua Status</option>
                        <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $statusFilter === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ $statusFilter === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $statusFilter === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Field Filter -->
                <div>
                    <label class="text-[11px] text-slate-400 block mb-1">Pilih Lapangan:</label>
                    <select 
                        name="field_id" 
                        onchange="this.form.submit()"
                        class="bg-slate-950 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 focus:outline-none focus:border-emerald-500 font-semibold"
                    >
                        <option value="">Semua Lapangan</option>
                        @foreach($fields as $f)
                            <option value="{{ $f->id }}" {{ $fieldFilter == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Reset Button -->
                <div class="self-end pt-1">
                    <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold border border-slate-700 inline-block transition">
                        Reset Filter
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-bold tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-5 py-3.5">Kode</th>
                        <th class="px-5 py-3.5">Pemesan</th>
                        <th class="px-5 py-3.5">Lapangan</th>
                        <th class="px-5 py-3.5">Jadwal Main</th>
                        <th class="px-5 py-3.5">Nominal</th>
                        <th class="px-5 py-3.5">Status Booking</th>
                        <th class="px-5 py-3.5">Pembayaran</th>
                        <th class="px-5 py-3.5 text-right">Aksi Cepat Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-5 py-3.5 font-mono font-bold text-emerald-400">
                                <a href="{{ route('bookings.show', $booking->booking_code) }}" class="hover:underline">
                                    {{ $booking->booking_code }}
                                </a>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-white">{{ $booking->user->name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $booking->user->phone ?? $booking->user->email }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-medium text-white">{{ $booking->field->name }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-medium text-white">{{ $booking->booking_date->format('d/m/Y') }}</div>
                                <div class="text-[11px] text-emerald-400 font-semibold">{{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }} WIB</div>
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-white">
                                Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    {{ $booking->status === 'confirmed' ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : '' }}
                                    {{ $booking->status === 'pending' ? 'bg-amber-950 text-amber-400 border border-amber-500/30' : '' }}
                                    {{ $booking->status === 'completed' ? 'bg-blue-950 text-blue-400 border border-blue-500/30' : '' }}
                                    {{ $booking->status === 'cancelled' ? 'bg-red-950 text-red-400 border border-red-500/30' : '' }}
                                ">
                                    {{ $booking->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    {{ $booking->payment_status === 'paid' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}
                                ">
                                    {{ $booking->payment_status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    @if($booking->status === 'pending')
                                        <form action="{{ route('admin.bookings.updateStatus', $booking->booking_code) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" title="Konfirmasi Booking" class="px-2.5 py-1 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-[10px] transition">
                                                Konfirmasi
                                            </button>
                                        </form>
                                    @endif

                                    @if($booking->status === 'confirmed')
                                        <form action="{{ route('admin.bookings.updateStatus', $booking->booking_code) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" title="Tandai Main Selesai" class="px-2.5 py-1 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-bold text-[10px] transition">
                                                Check-in / Selesai
                                            </button>
                                        </form>
                                    @endif

                                    @if($booking->status !== 'cancelled' && $booking->status !== 'completed')
                                        <form action="{{ route('admin.bookings.updateStatus', $booking->booking_code) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan?')">
                                            @csrf
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" title="Batalkan" class="p-1 rounded-lg bg-red-950 text-red-400 hover:bg-red-900 text-[10px] border border-red-800 transition">
                                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-slate-400">
                                Tidak ada data reservasi yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-slate-800">
            {{ $bookings->links() }}
        </div>
    </div>
</div>
@endsection
