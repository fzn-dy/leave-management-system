# Leave Management System - Energeek Technical Test

Aplikasi manajemen pengajuan cuti karyawan (Leave Request) yang dibangun sebagai bagian dari Technical Test untuk posisi Junior Fullstack Web Developer. Aplikasi ini menggunakan Laravel 12 sebagai Backend API dan Vue 3 + TypeScript sebagai Frontend.

## Fitur Utama
- Autentikasi & Otorisasi: Login menggunakan Laravel Sanctum dengan pemisahan akses antara Admin dan Karyawan.
- Manajemen User (Admin): CRUD User (Create, Read, Update, Soft Delete).
- Pengajuan Cuti (Karyawan): Input tanggal cuti, perhitungan otomatis total hari, dan pengecekan overlap tanggal.
- Persetujuan Cuti (Admin): Fitur Approve atau Reject pengajuan dari karyawan.
- Sisa Kuota Cuti: Pengurangan kuota otomatis setiap kali cuti disetujui.
- Riwayat Cuti: Tampilan status pengajuan (Pending, Approved, Rejected, Cancelled).

## Tech Stack
- Backend: Laravel 12, PHP 8.2+, PostgreSQL.
- Frontend: Vue 3 (Composition API), TypeScript, Tailwind CSS v4, Pinia (State Management).
- Containerization: Docker (Laravel Sail).

## Cara Instalasi & Menjalankan Aplikasi

### 1. Persyaratan Sistem
- Docker Desktop terinstal di komputer.
- Node.js & npm (untuk sisi client).

### 2. Setup Backend (Laravel Sail)
Buka terminal di root folder project:
1. cp .env.example .env
2. ./vendor/bin/sail up -d
3. ./vendor/bin/sail artisan key:generate
4. ./vendor/bin/sail artisan migrate:fresh --seed

### 3. Setup Frontend (Vue 3 Client)
Buka terminal baru di dalam folder client:
1. cd client
2. npm install
3. npm run dev

Aplikasi frontend akan berjalan di http://localhost:5173.

## Menjalankan Testing

### Unit Testing Backend (PHPUnit)
./vendor/bin/sail artisan test

### Unit Testing Frontend (Vitest)
cd client && npm run test:unit

## Akun Uji Coba (Default Seeder)
Administrator:
- Email: admin@energeek.id
- Password: password

Karyawan:
- Email: user@energeek.id
- Password: password

## Struktur Folder (Frontend)
Sesuai dengan Guide To Work yang diminta:
- src/components/: Komponen UI reusable.
- src/views/: Halaman utama aplikasi.
- src/routes/: Konfigurasi Vue Router & Navigation Guard.
- src/services/: Komunikasi API menggunakan Axios.
- src/stores/: State management menggunakan Pinia.
- src/types/: Definisi Interface TypeScript.

# AI Usage Disclosure

Dokumen ini mencantumkan penggunaan AI sebagai asisten kolaboratif dalam pengerjaan Technical Test Junior Fullstack Web Developer di Energeek.

## Asisten AI yang Digunakan
- Model: Gemini 3 Flash
- Platform: Gemini for Web

## Daftar Prompt Utama & Kegunaan
1. Analisis Brief & Database: "Bantu buatkan ERD dan Migrasi Laravel 12 untuk sistem cuti sesuai brief..."
2. Business Logic: "Bantu buatkan logic decrement balance di Controller dengan proteksi Race Condition menggunakan lockForUpdate."
3. Frontend Architecture: "Setup Vue 3 + TypeScript dengan struktur folder components, services, types, dan routes sesuai brief."
4. Mockup Styling: "Sesuaikan tampilan LoginView.vue agar mirip dengan mockup LeaveHub menggunakan Tailwind CSS v4."
5. Bug Fixing: "Selesaikan error Vite [plugin:vite:import-analysis] terkait file CSS yang hilang."

## Peran AI dalam Proyek
AI digunakan untuk mempercepat pembuatan boilerplate, memberikan saran best-practice (seperti database transaction), dan membantu troubleshooting konfigurasi TypeScript/Vite. Seluruh logika bisnis, struktur folder, dan implementasi UI telah ditinjau kembali dan disesuaikan secara manual agar memenuhi seluruh kriteria dalam Project Brief.