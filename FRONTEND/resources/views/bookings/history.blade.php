@extends('layouts.app')

@section('title', 'Riwayat Booking Saya - KickSwift')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <span class="text-xs font-bold uppercase tracking-widest text-emerald-400">Akun Pengguna</span>
            <h1 class="font-display font-extrabold text-3xl text-white mt-1">Riwayat Pemesanan Lapangan</h1>
        </div>
        <a href="{{ route('home') }}#katalog" class="px-5 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 flex items-center gap-1.5 transition">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Booking Lapangan Baru
        </a>
    </div>

    <div class="rounded-3xl bg-[#0e1626] border border-slate-800 overflow-hidden shadow-2xl">
        @if($bookings->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/90 text-slate-400 uppercase text-[10px] font-bold tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Kode Booking</th>
                            <th class="px-6 py-4">Lapangan</th>
                            <th class="px-6 py-4">Tanggal & Jam</th>
                            <th class="px-6 py-4">Total Biaya</th>
                            <th class="px-6 py-4">Status Booking</th>
                            <th class="px-6 py-4">Pembayaran</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @foreach($bookings as $item)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-6 py-4 font-mono font-bold text-emerald-400">
                                    {{ $item->booking_code }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-white">{{ $item->field->name }}</div>
                                    <div class="text-[11px] text-slate-400 uppercase">{{ str_replace('_', ' ', $item->field->type) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-white">{{ $item->booking_date->format('d M Y') }}</div>
                                    <div class="text-[11px] text-slate-400">{{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }} WIB ({{ $item->duration_hours }} Jam)</div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-white">
                                    Rp {{ number_format($item->total_price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase
                                        {{ $item->status === 'confirmed' ? 'bg-emerald-950 text-emerald-400 border border-emerald-500/30' : '' }}
                                        {{ $item->status === 'pending' ? 'bg-amber-950 text-amber-400 border border-amber-500/30' : '' }}
                                        {{ $item->status === 'completed' ? 'bg-blue-950 text-blue-400 border border-blue-500/30' : '' }}
                                        {{ $item->status === 'cancelled' ? 'bg-red-950 text-red-400 border border-red-500/30' : '' }}
                                    ">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase
                                        {{ $item->payment_status === 'paid' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}
                                    ">
                                        {{ $item->payment_status === 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('bookings.show', $item->booking_code) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-emerald-950 hover:text-emerald-300 text-slate-200 border border-slate-700 transition">
                                        <span>Detail / Tiket</span>
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-800">
                {{ $bookings->links() }}
            </div>
        @else
            <div class="p-16 text-center">
                <i data-lucide="calendar-x" class="w-12 h-12 text-slate-500 mx-auto mb-3"></i>
                <h3 class="font-bold text-white text-base">Belum Ada Riwayat Pemesanan</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    Anda belum melakukan booking lapangan. Pilih lapangan futsal favorit Anda sekarang!
                </p>
                <a href="{{ route('home') }}#katalog" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-500 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20">
                    Mulai Booking Sekarang
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
