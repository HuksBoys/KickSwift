@extends('layouts.app')

@section('title', 'Masuk Akun - KickSwift')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="p-8 rounded-3xl bg-[#0e1626] border border-slate-800 shadow-2xl">
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center mx-auto mb-3 border border-emerald-500/30">
                <i data-lucide="log-in" class="w-6 h-6"></i>
            </div>
            <h1 class="font-display font-extrabold text-2xl text-white">Masuk ke KickSwift</h1>
            <p class="text-xs text-slate-400 mt-1">Kelola jadwal booking dan tim futsal Anda</p>
        </div>

        <!-- Quick Demo Switchers -->
        <div class="mb-6 p-3 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-2">
            <p class="text-[11px] font-bold text-slate-400 uppercase text-center">Login Instan Demo (Tanpa Ketik):</p>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('demo.customer') }}" class="px-3 py-2 rounded-xl bg-emerald-950/60 hover:bg-emerald-900/80 text-emerald-300 border border-emerald-500/30 text-xs font-bold text-center transition">
                    👤 Demo Customer
                </a>
                <a href="{{ route('demo.admin') }}" class="px-3 py-2 rounded-xl bg-amber-950/60 hover:bg-amber-900/80 text-amber-300 border border-amber-500/30 text-xs font-bold text-center transition">
                    🛡️ Demo Admin
                </a>
            </div>
        </div>

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="text-xs font-semibold text-slate-300 block mb-1.5">Alamat Email:</label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email', 'customer@kickswift.com') }}" 
                    required 
                    class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl p-3 focus:outline-none focus:border-emerald-500 font-medium"
                >
                @error('email')
                    <span class="text-[11px] text-red-400 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-300 block mb-1.5">Password:</label>
                <input 
                    type="password" 
                    name="password" 
                    value="password" 
                    required 
                    class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl p-3 focus:outline-none focus:border-emerald-500 font-medium"
                >
            </div>

            <div class="flex items-center justify-between text-xs text-slate-400">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded bg-slate-900 border-slate-700 text-emerald-500 focus:ring-0">
                    <span>Ingat Saya</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-display font-extrabold text-xs shadow-lg shadow-emerald-500/25 transition">
                Masuk Sekarang
            </button>
        </form>

        <p class="mt-6 text-center text-xs text-slate-400">
            Belum memiliki akun? 
            <a href="{{ route('register') }}" class="text-emerald-400 font-bold hover:underline">Daftar Akun Baru</a>
        </p>
    </div>
</div>
@endsection
