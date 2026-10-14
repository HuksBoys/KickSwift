# ⚽ KickSwift - Prototype & MVP Dokumentasi Proyek

KickSwift adalah prototipe aplikasi web pemesanan (booking) lapangan futsal modern berbasis monorepo. Sistem ini menggabungkan keandalan **Laravel (PHP)** sebagai backend monolitik & antarmuka utama pengguna dengan performa tinggi **Rust (Axum)** sebagai microservice AI Chatbot cerdas yang terintegrasi langsung dengan **Ollama LLM**.

---

## 🏗️ 1. Arsitektur Monorepo

Struktur direktori proyek dirancang modular dan terpisah dengan jelas:

```text
KickSwift/
├── FRONTEND/                  # Inti Aplikasi Laravel (PHP 8.3+, Blade, Tailwind CSS, Alpine.js, DB Migrations)
│   ├── app/
│   │   ├── Http/Controllers/  # HomeController, FieldController, BookingController, AiChatController, AuthController, Admin\DashboardController
│   │   ├── Models/            # User, Field, Schedule, Booking
│   │   └── Services/          # BookingService (Concurrency Lock, Slot Matrix), AiChatService (Rust Proxy & Fallback)
│   ├── database/
│   │   ├── migrations/        # Tabel: users, fields, schedules, bookings
│   │   └── seeders/           # Seeder data awal admin, customer, dan 3 varian lapangan
│   ├── resources/views/       # Tampilan UI modern (Layout, Home, Field Detail, Receipt QRIS, History, Admin Dashboard, Auth)
│   ├── routes/
│   │   ├── web.php            # Rute tampilan web & aksi controller
│   │   └── api.php            # Rute REST API publik & terlindungi
│   └── .env                   # Konfigurasi database MySQL & environment
│
├── BACKEND/                   # Layanan Mikro Rust (Axum, Tokio, Ollama Integration)
│   ├── src/
│   │   ├── config.rs          # Konfigurasi environment (Host, Port, Ollama URL, Model)
│   │   ├── handlers/          # Controller HTTP Axum (health check, chat AI)
│   │   ├── models/            # DTO Request & Response
│   │   ├── services/          # Ollama LLM Client & business logic
│   │   └── main.rs            # Entry point, router, middleware CORS & tracing
│   ├── Cargo.toml             # Dependencies Rust
│   ├── Modelfile              # Blueprint kustomisasi persona AI KickSwift di Ollama
│   └── .env                   # Konfigurasi Rust service & Ollama model
│
└── KickSwift.md               # Dokumentasi utama proyek & log perubahan
```

---

## 🗄️ 2. Rancangan Database (MySQL)

Database MySQL `KickSwift` dirancang dengan relasi yang saling terhubung dan terindeks untuk mencegah *double-booking* serta mendukung penetapan tarif dinamis (*peak/off-peak*).

```mermaid
erDiagram
    USERS ||--o{ BOOKINGS : "makes"
    FIELDS ||--o{ SCHEDULES : "has"
    FIELDS ||--o{ BOOKINGS : "booked_in"

    USERS {
        bigint id PK
        string name
        string email UK
        string phone
        enum role "admin, customer, staff"
        string password
        timestamp created_at
    }

    FIELDS {
        bigint id PK
        string name
        string slug UK
        enum type "vinyl, synthetic_grass, interlocking, cement"
        text description
        decimal price_per_hour
        string image
        boolean is_active
        timestamp created_at
    }

    SCHEDULES {
        bigint id PK
        bigint field_id FK
        tinyint day_of_week "0=Sunday .. 6=Saturday"
        date date "opsional: libur/khusus"
        time start_time
        time end_time
        decimal custom_price "peak hour price"
        boolean is_available
    }

    BOOKINGS {
        bigint id PK
        string booking_code UK "KS-YYYYMMDD-XXXX"
        bigint user_id FK
        bigint field_id FK
        date booking_date
        time start_time
        time end_time
        int duration_hours
        decimal price_per_hour
        decimal total_price
        enum status "pending, confirmed, completed, cancelled"
        enum payment_status "unpaid, paid, refunded, failed"
        string payment_method
        string payment_reference
        timestamp paid_at
    }
```

---

## 🔄 3. Alur Komunikasi Layanan (Laravel ↔ Rust ↔ Ollama)

```mermaid
sequenceDiagram
    autonumber
    actor Customer as 👤 Pengguna (Browser)
    participant Laravel as 🌐 FRONTEND (Laravel)
    participant Rust as 🦀 BACKEND (Rust Axum :5000)
    participant Ollama as 🦙 Ollama Local LLM (:11434)
    participant MySQL as 🗄️ MySQL Database (:3306)

    Note over Customer, Laravel: 1. Alur Konsultasi AI Assistant
    Customer->>Laravel: Buka Floating Chatbot Widget di kanan bawah
    Customer->>Laravel: "Berapa tarif lapangan vinyl dan jam berapa masih kosong?"
    Laravel->>MySQL: Ambil data ketersediaan lapangan & harga
    MySQL-->>Laravel: Data lapangan terkini
    Laravel->>Rust: POST /api/v1/chat { prompt, field_context }
    Rust->>Ollama: POST /api/chat (Prompt + System Persona KickSwift)
    Ollama-->>Rust: Respon teks cerdas & rekomendasi
    Rust-->>Laravel: { status: "success", response: "...", suggested_intent: "VIEW_SCHEDULE" }
    Laravel-->>Customer: Tampilkan jawaban asisten di bubble chat

    Note over Customer, Laravel: 2. Alur Booking Lapangan Interaktif
    Customer->>Laravel: Pilih Tanggal & Klik Slot Waktu (08:00 - 23:00)
    Laravel->>MySQL: Lock baris jadwal via Pessimistic Lock (Cegah Double Booking)
    Laravel->>MySQL: Simpan booking (status: pending, payment_status: unpaid)
    Laravel-->>Customer: Tampilkan Tiket Digital & Simulasi Pembayaran QRIS
    Customer->>Laravel: Klik "Bayar Sekarang (Simulasi QRIS Sukses)"
    Laravel->>MySQL: Update status -> confirmed & payment_status -> paid
    Laravel-->>Customer: Tiket Terkonfirmasi & Jadwal Terkunci
```

---

## 🔌 4. Dokumentasi Rute & Endpoint

### A. Rute Web (Laravel Interface)
| Method | Rute | Deskripsi | Akses |
| :--- | :--- | :--- | :--- |
| `GET` | `/` | Halaman Landing & Katalog Lapangan Futsal | Publik |
| `GET` | `/fields/{slug}` | Detail Lapangan & Studio Pemilihan Slot Jadwal | Publik |
| `POST` | `/bookings` | Submit reservasi lapangan baru | Customer / Guest |
| `GET` | `/bookings/{code}` | Halaman Tiket Reservasi & Simulasi QRIS | Publik / Pemesan |
| `POST` | `/bookings/{code}/pay` | Eksekusi pembayaran simulasi (Konfirmasi Lunas) | Pemesan |
| `POST` | `/bookings/{code}/cancel`| Pembatalan reservasi yang belum dipakai | Pemesan |
| `GET` | `/my-bookings` | Riwayat seluruh reservasi pengguna | Terautentikasi |
| `POST` | `/ai/chat` | Endpoint AJAX Chatbot Assistant | Publik |
| `GET` | `/admin/dashboard` | Dashboard Operasional & Metrik Pengelola | Khusus Admin |
| `POST` | `/admin/bookings/{code}/status` | Update status booking (Konfirmasi / Selesai) | Khusus Admin |
| `GET` | `/demo/customer` | Login instan 1-klik sebagai Demo Customer | Demo Testing |
| `GET` | `/demo/admin` | Login instan 1-klik sebagai Demo Admin | Demo Testing |

### B. REST API Endpoints (`routes/api.php`)
| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/api/fields` | Mengambil daftar seluruh lapangan futsal aktif |
| `GET` | `/api/fields/{slug}/slots?date=YYYY-MM-DD` | Mengambil status 15 slot waktu (Tersedia / Terisi) |
| `POST` | `/api/bookings/calculate-price` | Kalkulasi estimasi total biaya (termasuk peak hours) |
| `POST` | `/api/ai/chat` | REST API AI Chatbot dengan konteks database |

### C. Rust AI Microservice (Port: `5000`)
| Method | Endpoint | Deskripsi |
| :--- | :--- | :--- |
| `GET` | `/health` / `/api/v1/health` | Health check mikroservis Rust |
| `POST` | `/api/v1/chat` | Integrasi komunikasi langsung dengan Ollama LLM |

---

## 🚀 5. Panduan Menjalankan & Menguji Prototipe

### 1. Menjalankan Laravel Web Server (`/FRONTEND`)
```bash
cd FRONTEND
php artisan serve
# Web app berjalan di: http://127.0.0.1:8000
```

### 2. Menjalankan Microservice Rust (`/BACKEND`)
```bash
cd BACKEND
cargo run
# Rust AI service berjalan di: http://127.0.0.1:5000
```

### 3. Menggunakan Model Ollama Anda
Model lokal Anda (contoh `qwen2.5:1.5b`) sudah otomatis dikonfigurasikan di `BACKEND/.env`.
```bash
# Pastikan Ollama aktif di latar belakang:
ollama list
```

---

## 🧪 6. Skenario Pengujian Prototipe (End-to-End Walkthrough)

1. **Eksplorasi Katalog**:
   - Buka `http://127.0.0.1:8000`.
   - Coba filter tipe lapangan (*Vinyl Pro*, *Rumput Sintetis*, *Interlocking*).
2. **Uji AI Assistant Chatbot**:
   - Klik ikon pesan mengambang di pojok kanan bawah.
   - Ajukan pertanyaan seperti *"Berapa tarif lapangan vinyl?"* atau klik tombol pertanyaan cepat.
3. **Pemesanan Lapangan (Interactive Slot Picker)**:
   - Pilih lapangan -> Klik **Pilih Jadwal**.
   - Pilih tanggal -> Klik salah satu slot waktu hijau (misal `19:00 - 20:00`).
   - Perhatikan kalkulasi harga otomatis (sudah memperhitungkan tarif *peak hours* malam).
   - Klik **Konfirmasi Booking Sekarang**.
4. **Pembayaran & Verifikasi QRIS**:
   - Di halaman tiket, klik **Bayar Sekarang (Simulasi QRIS Sukses)**.
   - Status langsung berubah menjadi **LUNAS / CONFIRMED** dan slot waktu di lapangan tersebut otomatis berubah menjadi merah (*Terisi/Booked*) jika dicek kembali.
5. **Akses Dashboard Admin**:
   - Klik dropdown role di navbar -> Pilih **Masuk sbg Admin** (atau buka `/admin/dashboard`).
   - Pantau metrik total omset, jumlah booking harian, filter tanggal, dan konfirmasi/check-in pemain di meja kasir.

---

## 📝 7. Changelog & Riwayat Pembaruan

### [Fase 2] - 2026-10-09
- **Service Layer**:
  - `BookingService`: Logika penghitungan ketersediaan slot, pencegahan *race-condition* double booking dengan *Pessimistic Locking*, penghitungan tarif dinamis *peak hours*, dan simulasi pembayaran instan.
  - `AiChatService`: Pengumpul konteks data lapangan dan jembatan ke mikroservis Rust dengan sistem *fallback rule engine* cerdas.
- **Controller & Routing**:
  - `HomeController`, `FieldController`, `BookingController`, `AiChatController`, `Admin\DashboardController`, dan `AuthController`.
  - Pendaftaran 26 rute Web dan REST API.
- **Frontend UI / Blade**:
  - `layouts/app.blade.php`: Desain bertema gelap futuristik (*futsal aesthetic*) dengan Tailwind CSS, Alpine.js, Lucide Icons, notifikasi flash, dan **Floating AI Assistant Chatbot Widget**.
  - `home.blade.php`: Halaman landing interaktif dengan hero, statistik, filter varian lapangan, dan alur booking.
  - `fields/show.blade.php`: Studio pemilihan jadwal visual dengan matriks slot warna (Tersedia, Terisi, Dipilih, Peak Hour) dan kalkulator harga real-time.
  - `bookings/receipt.blade.php`: Halaman tiket digital dengan barcode QRIS simulasi dan tombol konfirmasi instan.
  - `bookings/history.blade.php`: Halaman riwayat reservasi pelanggan.
  - `admin/dashboard.blade.php`: Panel kontrol pengelola futsal dengan metrik omset, filter jadwal harian, dan kontrol check-in.
  - `auth/login.blade.php` & `auth/register.blade.php`: Autentikasi dengan fitur *1-Click Demo Login*.
- **Integrasi Model Kustom Ollama**: Penambahan template `BACKEND/Modelfile` dan konfigurasi environment untuk mendukung model lokal pengguna (`qwen2.5:1.5b`).

### [Fase 1] - 2026-10-09
- Setup struktur monorepo `/FRONTEND` dan `/BACKEND`.
- Pembuatan 4 migrasi database dan Eloquent Model (`users`, `fields`, `schedules`, `bookings`).
- Seeder data awal (Admin, Customer, 3 Lapangan, 315 Slot Jadwal).
- Arsitektur dasar layanan mikro Rust Axum.
- Dokumentasi awal `KickSwift.md`.
