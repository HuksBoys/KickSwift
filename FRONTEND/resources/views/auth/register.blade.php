@extends('layouts.app')

@section('title', 'Daftar Akun Baru - KickSwift')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="p-8 rounded-3xl bg-[#0e1626] border border-slate-800 shadow-2xl">
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center mx-auto mb-3 border border-emerald-500/30">
                <i data-lucide="user-plus" class="w-6 h-6"></i>
            </div>
            <h1 class="font-display font-extrabold text-2xl text-white">Daftar Akun KickSwift</h1>
            <p class="text-xs text-slate-400 mt-1">Dapatkan kemudahan booking lapangan futsal & diskon</p>
        </div>

        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="text-xs font-semibold text-slate-300 block mb-1.5">Nama Lengkap / Nama Tim:</label>
                <input 
                    type="text" 
                    name="name" 
                    value="{{ old('name') }}" 
                    required 
                    placeholder="Contoh: FC Garuda / Ahmad" 
                    class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl p-3 focus:outline-none focus:border-emerald-500 font-medium"
                >
                @error('name')
                    <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-300 block mb-1.5">Alamat Email:</label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    placeholder="nama@email.com" 
                    class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl p-3 focus:outline-none focus:border-emerald-500 font-medium"
                >
                @error('email')
                    <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-300 block mb-1.5">Nomor WhatsApp / HP:</label>
                <input 
                    type="tel" 
                    name="phone" 
                    value="{{ old('phone') }}" 
                    placeholder="081234567890" 
                    class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl p-3 focus:outline-none focus:border-emerald-500 font-medium"
                >
                @error('phone')
                    <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-300 block mb-1.5">Password:</label>
                <input 
                    type="password" 
                    name="password" 
                    required 
                    placeholder="Minimal 6 karakter" 
                    class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl p-3 focus:outline-none focus:border-emerald-500 font-medium"
                >
                @error('password')
                    <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-300 block mb-1.5">Konfirmasi Password:</label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    required 
                    placeholder="Ulangi password" 
                    class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl p-3 focus:outline-none focus:border-emerald-500 font-medium"
                >
            </div>

            <button type="submit" class="w-full py-3.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-display font-extrabold text-xs shadow-lg shadow-emerald-500/25 transition">
                Daftar Sekarang
            </button>
        </form>

        <p class="mt-6 text-center text-xs text-slate-400">
            Sudah memiliki akun? 
            <a href="{{ route('login') }}" class="text-emerald-400 font-bold hover:underline">Masuk di Sini</a>
        </p>
    </div>
</div>
@endsection
