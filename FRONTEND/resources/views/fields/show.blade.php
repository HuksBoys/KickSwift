@extends('layouts.app')

@section('title', 'Booking ' . $field->name . ' - KickSwift')

@section('content')
<div x-data="fieldBookingApp()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Breadcrumbs & Header -->
    <div class="mb-6 flex items-center space-x-2 text-xs text-slate-400">
        <a href="{{ route('home') }}" class="hover:text-emerald-400 transition">Beranda</a>
        <span>/</span>
        <a href="{{ route('home') }}#katalog" class="hover:text-emerald-400 transition">Katalog Lapangan</a>
        <span>/</span>
        <span class="text-emerald-400 font-semibold">{{ $field->name }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: Field Detail & Interactive Slot Matrix (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Field Summary Card -->
            <div class="p-6 sm:p-8 rounded-3xl bg-[#0e1626] border border-slate-800">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-950 text-emerald-400 border border-emerald-500/30">
                        {{ str_replace('_', ' ', $field->type) }}
                    </span>
                    <span class="text-xs text-slate-400">
                        Buka Setiap Hari: <strong>08:00 - 23:00 WIB</strong>
                    </span>
                </div>

                <h1 class="font-display font-extrabold text-2xl sm:text-4xl text-white">
                    {{ $field->name }}
                </h1>

                <p class="mt-3 text-slate-300 text-sm leading-relaxed">
                    {{ $field->description }}
                </p>

                <!-- Badges -->
                <div class="mt-6 pt-6 border-t border-slate-800 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800 text-center">
                        <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400 mx-auto mb-1"></i>
                        <span class="text-slate-300 font-medium">FIFA Approved</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800 text-center">
                        <i data-lucide="zap" class="w-4 h-4 text-amber-400 mx-auto mb-1"></i>
                        <span class="text-slate-300 font-medium">Lampu LED 500 Lux</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800 text-center">
                        <i data-lucide="wind" class="w-4 h-4 text-blue-400 mx-auto mb-1"></i>
                        <span class="text-slate-300 font-medium">Ventilasi Exhaust</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800 text-center">
                        <i data-lucide="sparkles" class="w-4 h-4 text-purple-400 mx-auto mb-1"></i>
                        <span class="text-slate-300 font-medium">Free Rompi & Bola</span>
                    </div>
                </div>
            </div>

            <!-- Interactive Slot Schedule Studio -->
            <div class="p-6 sm:p-8 rounded-3xl bg-[#0e1626] border border-slate-800">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="font-display font-bold text-xl text-white flex items-center gap-2">
                            <i data-lucide="calendar" class="w-5 h-5 text-emerald-400"></i>
                            Pilih Tanggal & Slot Waktu
                        </h3>
                        <p class="text-xs text-slate-400 mt-1">Klik pada slot hijau yang masih tersedia untuk mulai booking</p>
                    </div>

                    <!-- Date Picker Input -->
                    <div class="flex items-center gap-2">
                        <label for="booking_date_picker" class="text-xs text-slate-400 font-medium">Tanggal:</label>
                        <input 
                            type="date" 
                            id="booking_date_picker"
                            x-model="selectedDate" 
                            @change="fetchSlots"
                            min="{{ date('Y-m-d') }}"
                            class="bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 focus:outline-none focus:border-emerald-500 font-semibold"
                        >
                    </div>
                </div>

                <!-- Legend Indicators -->
                <div class="flex flex-wrap items-center gap-4 text-xs mb-6 pb-4 border-b border-slate-800 text-slate-400">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded-md bg-slate-800 border border-slate-600"></span>
                        <span>Tersedia (Reguler)</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded-md bg-amber-950 border border-amber-600"></span>
                        <span>⭐ Peak Hour (Malam)</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded-md bg-emerald-500"></span>
                        <span class="text-emerald-300 font-semibold">Dipilih</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded-md bg-red-950/60 border border-red-800"></span>
                        <span class="text-red-400">Terisi / Penuh</span>
                    </div>
                </div>

                <!-- Slots Grid (Visual Interactive Picker) -->
                <div x-show="!isLoadingSlots" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <template x-for="slot in slots" :key="slot.start_time">
                        <button 
                            type="button"
                            @click="selectSlot(slot)"
                            :disabled="!slot.is_available"
                            :class="{
                                'bg-emerald-500 text-slate-950 font-bold shadow-lg shadow-emerald-500/25 border-emerald-400 ring-2 ring-emerald-400 scale-[1.02]': selectedStartTime === slot.start_time,
                                'bg-red-950/40 text-red-400 border-red-900/50 cursor-not-allowed opacity-60 line-through': !slot.is_available,
                                'bg-amber-950/30 text-amber-300 border-amber-800/60 hover:border-emerald-500': slot.is_available && slot.is_peak && selectedStartTime !== slot.start_time,
                                'bg-slate-900/90 text-slate-200 border-slate-700/80 hover:border-emerald-500 hover:bg-slate-800': slot.is_available && !slot.is_peak && selectedStartTime !== slot.start_time
                            }"
                            class="p-3.5 rounded-2xl border text-left transition-all duration-200 flex flex-col justify-between h-20"
                        >
                            <div class="flex items-center justify-between">
                                <span class="font-display font-bold text-sm" x-text="slot.start_time + ' - ' + slot.end_time"></span>
                                <template x-if="slot.is_peak && slot.is_available">
                                    <span class="text-[9px] px-1 py-0.5 rounded bg-amber-900/80 text-amber-300 font-bold">PEAK</span>
                                </template>
                            </div>
                            <div class="text-[11px] font-medium" :class="selectedStartTime === slot.start_time ? 'text-slate-900' : 'text-slate-400'" x-text="slot.is_available ? slot.formatted_price : 'Sudah Dipesan'"></div>
                        </button>
                    </template>
                </div>

                <!-- Skeleton Loading -->
                <div x-show="isLoadingSlots" class="grid grid-cols-2 sm:grid-cols-4 gap-3 py-6 text-center text-xs text-slate-400">
                    <i data-lucide="loader-2" class="w-6 h-6 animate-spin mx-auto col-span-4 text-emerald-400 mb-2"></i>
                    <span class="col-span-4">Memuat slot jadwal ketersediaan...</span>
                </div>
            </div>
        </div>

        <!-- Right Column: Booking Summary & Checkout Form (4 cols) -->
        <div class="lg:col-span-4">
            <div class="sticky top-28 p-6 sm:p-8 rounded-3xl bg-[#0e1626] border border-slate-800/90 shadow-2xl">
                <h3 class="font-display font-bold text-xl text-white mb-4">
                    Ringkasan Pemesanan
                </h3>

                <form action="{{ route('bookings.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="field_id" value="{{ $field->id }}">
                    <input type="hidden" name="booking_date" :value="selectedDate">
                    <input type="hidden" name="start_time" :value="selectedStartTime">

                    <!-- Selected Field Name -->
                    <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-slate-800">
                        <span class="text-[10px] uppercase font-bold text-slate-400">Lapangan</span>
                        <div class="font-bold text-white text-sm mt-0.5">{{ $field->name }}</div>
                    </div>

                    <!-- Date & Time Row -->
                    <div class="grid grid-cols-2 gap-2">
                        <div class="p-3 rounded-2xl bg-slate-900/90 border border-slate-800">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Tanggal</span>
                            <div class="font-semibold text-white text-xs mt-0.5" x-text="formatDisplayDate(selectedDate)"></div>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-900/90 border border-slate-800">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Jam Mulai</span>
                            <div class="font-semibold text-emerald-400 text-xs mt-0.5" x-text="selectedStartTime ? selectedStartTime + ' WIB' : 'Pilih slot...'"></div>
                        </div>
                    </div>

                    <!-- Duration Selector -->
                    <div>
                        <label class="text-xs font-semibold text-slate-300 block mb-1.5">Durasi Main:</label>
                        <select 
                            name="duration_hours" 
                            x-model="durationHours" 
                            @change="recalculatePrice"
                            class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-emerald-500 font-semibold"
                        >
                            <option value="1">1 Jam</option>
                            <option value="2">2 Jam</option>
                            <option value="3">3 Jam</option>
                        </select>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="text-xs font-semibold text-slate-300 block mb-1.5">Metode Pembayaran:</label>
                        <select 
                            name="payment_method" 
                            class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-emerald-500 font-semibold"
                        >
                            <option value="qris">QRIS (Otomatis & Instan)</option>
                            <option value="bank_transfer">Transfer Bank (BCA / Mandiri / BRI)</option>
                            <option value="cash">Bayar di Tempat (Kasir)</option>
                        </select>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="text-xs font-semibold text-slate-300 block mb-1.5">Catatan Tambahan (Opsional):</label>
                        <textarea 
                            name="notes" 
                            rows="2" 
                            placeholder="Contoh: Perlu tambahan 2 bola & rompi hijau" 
                            class="w-full bg-slate-900 border border-slate-700 text-slate-200 text-xs rounded-xl p-3 focus:outline-none focus:border-emerald-500"
                        ></textarea>
                    </div>

                    <!-- Price Calculation Card -->
                    <div class="pt-4 border-t border-slate-800 space-y-2">
                        <div class="flex justify-between text-xs text-slate-400">
                            <span>Tarif Dasar / Jam</span>
                            <span>Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-slate-400">
                            <span>Total Durasi</span>
                            <span x-text="durationHours + ' Jam'"></span>
                        </div>
                        <div class="pt-2 border-t border-slate-800 flex justify-between items-baseline">
                            <span class="text-xs font-bold text-white uppercase">Total Bayar</span>
                            <span class="font-display font-extrabold text-xl text-emerald-400" x-text="formattedTotal"></span>
                        </div>
                    </div>

                    <!-- Submit Booking Button -->
                    <button 
                        type="submit" 
                        :disabled="!selectedStartTime"
                        class="w-full py-4 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-display font-extrabold text-sm shadow-xl shadow-emerald-500/25 disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-300 flex items-center justify-center gap-2"
                    >
                        <i data-lucide="check" class="w-4 h-4 stroke-[3]"></i>
                        <span>Konfirmasi Booking Sekarang</span>
                    </button>
                    
                    <p class="text-[11px] text-center text-slate-400">
                        🔒 Slot terkunci otomatis dan bebas risiko tabrakan jadwal.
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function fieldBookingApp() {
        return {
            fieldSlug: '{{ $field->slug }}',
            selectedDate: '{{ $selectedDate }}',
            selectedStartTime: '',
            durationHours: 1,
            slots: @json($slots),
            isLoadingSlots: false,
            basePricePerHour: {{ (float) $field->price_per_hour }},
            totalPrice: {{ (float) $field->price_per_hour }},
            formattedTotal: 'Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}',

            selectSlot(slot) {
                if (!slot.is_available) return;
                this.selectedStartTime = slot.start_time;
                this.recalculatePrice();
            },

            async fetchSlots() {
                this.isLoadingSlots = true;
                this.selectedStartTime = '';
                try {
                    const res = await fetch(`/fields/${this.fieldSlug}/slots?date=${this.selectedDate}`);
                    const json = await res.json();
                    if (json.status === 'success') {
                        this.slots = json.slots;
                    }
                } catch (e) {
                    console.error('Error fetching slots:', e);
                } finally {
                    this.isLoadingSlots = false;
                    this.$nextTick(() => {
                        lucide.createIcons();
                    });
                }
            },

            async recalculatePrice() {
                if (!this.selectedStartTime) {
                    const est = this.basePricePerHour * this.durationHours;
                    this.totalPrice = est;
                    this.formattedTotal = 'Rp ' + est.toLocaleString('id-ID');
                    return;
                }

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const res = await fetch('/api/bookings/calculate-price', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            field_id: {{ $field->id }},
                            booking_date: this.selectedDate,
                            start_time: this.selectedStartTime,
                            duration_hours: parseInt(this.durationHours),
                        })
                    });

                    const json = await res.json();
                    if (json.status === 'success') {
                        this.totalPrice = json.data.total_price;
                        this.formattedTotal = json.data.formatted_total;
                    }
                } catch (e) {
                    console.error('Price calculation error:', e);
                }
            },

            formatDisplayDate(dateStr) {
                if (!dateStr) return '';
                const parts = dateStr.split('-');
                return `${parts[2]}/${parts[1]}/${parts[0]}`;
            }
        };
    }
</script>
@endpush
