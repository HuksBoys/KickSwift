<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KickSwift - Platform Booking Lapangan Futsal Modern & AI Assistant')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            accent: '#10b981',
                            neon: '#22c55e',
                            dark: '#090d16',
                            card: '#111827',
                            border: '#1f2937',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            background-color: #080c14;
            color: #f3f4f6;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .glow-neon {
            box-shadow: 0 0 25px rgba(34, 197, 94, 0.25);
        }
        .glow-card:hover {
            box-shadow: 0 10px 30px -10px rgba(16, 185, 129, 0.3);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-[#080c14] text-slate-100 antialiased selection:bg-emerald-500 selection:text-black">

    <!-- Top Navigation Bar -->
    <nav class="sticky top-0 z-40 backdrop-blur-xl bg-[#0b111e]/90 border-b border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-600 via-emerald-500 to-teal-400 p-0.5 shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition-transform duration-300">
                        <div class="w-full h-full bg-[#080c14] rounded-[14px] flex items-center justify-center">
                            <i data-lucide="zap" class="w-6 h-6 text-emerald-400 fill-emerald-400/30"></i>
                        </div>
                    </div>
                    <div>
                        <span class="font-display font-extrabold text-2xl tracking-tight bg-gradient-to-r from-white via-slate-100 to-slate-400 bg-clip-text text-transparent">
                            Kick<span class="text-emerald-400">Swift</span>
                        </span>
                        <span class="hidden sm:inline-block ml-2 text-[10px] font-bold tracking-widest uppercase px-2 py-0.5 rounded-full bg-emerald-950/80 text-emerald-400 border border-emerald-500/30">
                            MVP Pro
                        </span>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-sm font-semibold {{ request()->routeIs('home') ? 'text-emerald-400' : 'text-slate-300 hover:text-white' }} transition-colors">
                        Katalog Lapangan
                    </a>
                    <a href="{{ route('bookings.history') }}" class="text-sm font-semibold {{ request()->routeIs('bookings.history') ? 'text-emerald-400' : 'text-slate-300 hover:text-white' }} transition-colors">
                        Riwayat Booking
                    </a>
                    @if(Auth::check() && Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-amber-400 hover:text-amber-300 flex items-center gap-1.5 px-3 py-1 rounded-lg bg-amber-950/40 border border-amber-500/30">
                            <i data-lucide="shield-alert" class="w-4 h-4"></i>
                            Admin Panel
                        </a>
                    @endif
                </div>

                <!-- Right Actions / Auth -->
                <div class="flex items-center space-x-3">
                    <!-- Quick Demo Role Switcher dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="text-xs font-semibold px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 border border-slate-700 flex items-center gap-1.5 transition">
                            <i data-lucide="shuffle" class="w-3.5 h-3.5 text-emerald-400"></i>
                            <span class="hidden sm:inline">Role:</span> 
                            <strong class="text-emerald-400">{{ Auth::check() ? ucfirst(Auth::user()->role) : 'Guest' }}</strong>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-52 rounded-2xl bg-[#0f172a] border border-slate-700 shadow-2xl p-2 z-50">
                            <p class="text-[11px] text-slate-400 px-3 py-1 font-semibold uppercase tracking-wider">Ganti Role Demo:</p>
                            <a href="{{ route('demo.customer') }}" class="flex items-center gap-2 px-3 py-2 text-xs rounded-xl hover:bg-emerald-950/50 hover:text-emerald-300 text-slate-200 transition">
                                <i data-lucide="user" class="w-4 h-4 text-emerald-400"></i>
                                Masuk sbg Customer
                            </a>
                            <a href="{{ route('demo.admin') }}" class="flex items-center gap-2 px-3 py-2 text-xs rounded-xl hover:bg-amber-950/50 hover:text-amber-300 text-slate-200 transition">
                                <i data-lucide="shield-check" class="w-4 h-4 text-amber-400"></i>
                                Masuk sbg Admin
                            </a>
                        </div>
                    </div>

                    @if(Auth::check())
                        <div class="flex items-center space-x-3 pl-2 border-l border-slate-800">
                            <div class="hidden lg:block text-right">
                                <p class="text-xs font-bold text-slate-200 leading-tight">{{ Auth::user()->name }}</p>
                                <p class="text-[11px] text-slate-400">{{ Auth::user()->email }}</p>
                            </div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" title="Logout" class="p-2 rounded-xl bg-red-950/40 text-red-400 border border-red-800/40 hover:bg-red-900/60 transition">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-bold px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 transition border border-slate-700">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="text-xs font-bold px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-lg shadow-emerald-500/25 transition">
                            Daftar
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Global Alerts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full">
        @if(session('success'))
            <div class="p-4 mb-4 rounded-2xl bg-emerald-950/70 border border-emerald-500/40 text-emerald-200 flex items-center justify-between shadow-lg shadow-emerald-950/50">
                <div class="flex items-center space-x-3">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-400 shrink-0"></i>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-4 rounded-2xl bg-red-950/70 border border-red-500/40 text-red-200 flex items-center justify-between shadow-lg shadow-red-950/50">
                <div class="flex items-center space-x-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-400 shrink-0"></i>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-200"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 mb-4 rounded-2xl bg-blue-950/70 border border-blue-500/40 text-blue-200 flex items-center justify-between shadow-lg shadow-blue-950/50">
                <div class="flex items-center space-x-3">
                    <i data-lucide="info" class="w-5 h-5 text-blue-400 shrink-0"></i>
                    <p class="text-sm font-medium">{{ session('info') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-blue-400 hover:text-blue-200"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
        @endif
    </div>

    <!-- Main Content Body -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-24 border-t border-slate-800/80 bg-[#060910] text-slate-400 text-sm py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center border border-emerald-500/30">
                    <i data-lucide="zap" class="w-4 h-4 text-emerald-400"></i>
                </div>
                <span class="font-display font-bold text-lg text-white">Kick<span class="text-emerald-400">Swift</span></span>
                <span class="text-xs text-slate-500">© {{ date('Y') }} KickSwift Futsal Booking MVP. All rights reserved.</span>
            </div>
            <div class="flex items-center space-x-6 text-xs text-slate-400">
                <span>⚡ Powered by Laravel 13 & Rust Axum</span>
                <span>🦙 Ollama Local LLM</span>
            </div>
        </div>
    </footer>

    <!-- 🤖 FLOATING AI CHATBOT WIDGET (KickSwift Assistant) -->
    <div x-data="kickSwiftAiWidget()" class="fixed bottom-6 right-6 z-50 flex flex-col items-end">
        
        <!-- Chatbot Window -->
        <div 
            x-show="isOpen" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-8 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-8 scale-95"
            class="w-[380px] sm:w-[420px] h-[540px] rounded-3xl bg-[#0c1322] border border-slate-700/80 shadow-2xl flex flex-col overflow-hidden mb-4 backdrop-blur-2xl"
            style="display: none;"
        >
            <!-- Chat Header -->
            <div class="p-4 bg-gradient-to-r from-emerald-950 via-[#0e1829] to-[#0c1322] border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 p-0.5">
                            <div class="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center">
                                <i data-lucide="bot" class="w-5 h-5 text-emerald-400"></i>
                            </div>
                        </div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-[#0c1322] animate-pulse"></span>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-sm text-white flex items-center gap-1.5">
                            KickSwift AI Assistant
                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-900/60 text-emerald-300 border border-emerald-600/30">Rust & Ollama</span>
                        </h3>
                        <p class="text-[11px] text-slate-400">Tanya jadwal, rekomendasi lapangan & tarif</p>
                    </div>
                </div>
                <div class="flex items-center space-x-1">
                    <button @click="clearChat" title="Reset percakapan" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-200 hover:bg-slate-800 transition">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </button>
                    <button @click="isOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-200 hover:bg-slate-800 transition">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Messages Stream Area -->
            <div x-ref="chatContainer" class="flex-grow p-4 overflow-y-auto space-y-3.5 scrollbar-thin scrollbar-thumb-slate-800">
                <!-- Welcome bubble -->
                <div class="flex items-start space-x-2.5">
                    <div class="w-7 h-7 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center shrink-0 mt-0.5">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-emerald-400"></i>
                    </div>
                    <div class="bg-slate-800/90 text-slate-200 p-3.5 rounded-2xl rounded-tl-sm text-xs leading-relaxed border border-slate-700 max-w-[85%]">
                        👋 Halo! Saya **KickSwift AI Assistant**. Ada yang bisa saya bantu terkait pemilihan lapangan futsal (Vinyl, Rumput Sintetis, Interlocking) atau pengecekan jadwal?
                    </div>
                </div>

                <!-- Suggested Quick Prompts -->
                <div x-show="messages.length === 0" class="pt-2">
                    <p class="text-[11px] font-semibold text-slate-400 mb-2 px-1">Pertanyaan Cepat:</p>
                    <div class="flex flex-wrap gap-1.5">
                        <button @click="sendQuickPrompt('Berapa tarif lapangan vinyl dan rumput sintetis?')" class="text-[11px] text-left px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-emerald-950/60 hover:text-emerald-300 text-slate-300 border border-slate-700 transition">
                            💰 Cek daftar tarif lapangan
                        </button>
                        <button @click="sendQuickPrompt('Rekomendasi lapangan yang aman untuk persendian lutut')" class="text-[11px] text-left px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-emerald-950/60 hover:text-emerald-300 text-slate-300 border border-slate-700 transition">
                            👟 Lapangan paling empuk
                        </button>
                        <button @click="sendQuickPrompt('Apakah ada jadwal kosong untuk malam ini jam 19:00?')" class="text-[11px] text-left px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-emerald-950/60 hover:text-emerald-300 text-slate-300 border border-slate-700 transition">
                            📅 Cek jadwal jam malam
                        </button>
                    </div>
                </div>

                <!-- Chat Messages Loop -->
                <template x-for="(msg, index) in messages" :key="index">
                    <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex items-start space-x-2.5'">
                        <template x-if="msg.role === 'assistant'">
                            <div class="w-7 h-7 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="bot" class="w-3.5 h-3.5 text-emerald-400"></i>
                            </div>
                        </template>
                        <div 
                            :class="msg.role === 'user' 
                                ? 'bg-emerald-500 text-slate-950 font-medium rounded-2xl rounded-tr-sm p-3 text-xs leading-relaxed max-w-[85%] shadow-lg shadow-emerald-500/20' 
                                : 'bg-slate-800/90 text-slate-200 p-3.5 rounded-2xl rounded-tl-sm text-xs leading-relaxed border border-slate-700 max-w-[85%] whitespace-pre-line'"
                            x-text="msg.content"
                        ></div>
                    </div>
                </template>

                <!-- Loading Bubble -->
                <div x-show="isLoading" class="flex items-start space-x-2.5">
                    <div class="w-7 h-7 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center shrink-0">
                        <i data-lucide="bot" class="w-3.5 h-3.5 text-emerald-400 animate-spin"></i>
                    </div>
                    <div class="bg-slate-800/90 p-3 rounded-2xl rounded-tl-sm text-xs border border-slate-700 flex items-center space-x-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-bounce" style="animation-delay: 0ms"></span>
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-bounce" style="animation-delay: 150ms"></span>
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-bounce" style="animation-delay: 300ms"></span>
                    </div>
                </div>
            </div>

            <!-- Input Box -->
            <div class="p-3 bg-[#080d16] border-t border-slate-800">
                <form @submit.prevent="sendMessage" class="flex items-center space-x-2">
                    <input 
                        type="text" 
                        x-model="inputMessage" 
                        :disabled="isLoading"
                        placeholder="Ketik pertanyaan untuk AI..." 
                        class="flex-grow bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 disabled:opacity-50"
                    >
                    <button 
                        type="submit" 
                        :disabled="isLoading || !inputMessage.trim()"
                        class="px-3.5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs flex items-center justify-center disabled:opacity-40 disabled:cursor-not-allowed shadow-md shadow-emerald-500/20 transition"
                    >
                        <i data-lucide="send" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Floating Button -->
        <button 
            @click="toggleChat"
            class="group relative flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 via-emerald-500 to-teal-400 text-slate-950 shadow-xl shadow-emerald-500/30 hover:scale-105 active:scale-95 transition-all duration-300"
        >
            <div class="relative">
                <i x-show="!isOpen" data-lucide="message-square" class="w-6 h-6 stroke-[2.5]"></i>
                <i x-show="isOpen" data-lucide="x" class="w-6 h-6 stroke-[2.5]" style="display: none;"></i>
            </div>
            <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-300 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-teal-400 border-2 border-[#080c14]"></span>
            </span>
        </button>
    </div>

    <!-- Script for Alpine AI Chatbot -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('kickSwiftAiWidget', () => ({
                isOpen: false,
                isLoading: false,
                inputMessage: '',
                messages: [],

                toggleChat() {
                    this.isOpen = !this.isOpen;
                    if (this.isOpen) {
                        this.$nextTick(() => {
                            lucide.createIcons();
                            this.scrollToBottom();
                        });
                    }
                },

                sendQuickPrompt(text) {
                    this.inputMessage = text;
                    this.sendMessage();
                },

                async sendMessage() {
                    const text = this.inputMessage.trim();
                    if (!text || this.isLoading) return;

                    this.messages.push({ role: 'user', content: text });
                    this.inputMessage = '';
                    this.isLoading = true;

                    this.$nextTick(() => {
                        lucide.createIcons();
                        this.scrollToBottom();
                    });

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        
                        const response = await fetch('/ai/chat', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                prompt: text,
                                history: this.messages.slice(-6)
                            })
                        });

                        const data = await response.json();

                        if (data && data.response) {
                            this.messages.push({
                                role: 'assistant',
                                content: data.response
                            });
                        } else {
                            this.messages.push({
                                role: 'assistant',
                                content: 'Maaf, terjadi kendala saat memproses jawaban. Silakan coba kembali.'
                            });
                        }
                    } catch (error) {
                        console.error('Chat error:', error);
                        this.messages.push({
                            role: 'assistant',
                            content: 'Maaf, gagal tersambung dengan asisten AI. Pastikan server backend sedang aktif.'
                        });
                    } finally {
                        this.isLoading = false;
                        this.$nextTick(() => {
                            lucide.createIcons();
                            this.scrollToBottom();
                        });
                    }
                },

                clearChat() {
                    this.messages = [];
                },

                scrollToBottom() {
                    if (this.$refs.chatContainer) {
                        this.$refs.chatContainer.scrollTop = this.$refs.chatContainer.scrollHeight;
                    }
                }
            }));
        });

        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
    @stack('scripts')
</body>
</html>
