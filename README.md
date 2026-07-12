# SiDoRa – Aplikasi Manajemen Dokumen Administrasi Dosen

> Dokumen ini **khusus untuk tim pengembangan internal.**  
> Tujuannya agar seluruh anggota tim memiliki panduan kerja, struktur branch, dan aturan kontribusi yang konsisten selama proses pengembangan.

**SiDoRa** adalah aplikasi manajemen dokumen yang dirancang untuk memudahkan dosen dalam mengunggah dan mengelola dokumen administrasi seperti RPS, BKD, SKP, serta memberikan hak akses secara terstruktur kepada Tata Usaha (TU) dan Kepala Program Studi (Kaprodi).

---

## Struktur Tim & Pengembang

| Anggota | NIM | Program Studi |
|----------|-----|---------------|
| **Azkha Nazzala Prasadha Dies** | 241511069 | D3 Teknik Informatika - POLBAN (2025) |
| **Dzakir Tsabit Asy Syafiq** | 241511071 | D3 Teknik Informatika - POLBAN (2025) |
| **Ibnu Hilmi Athaillah** | 241511079 | D3 Teknik Informatika - POLBAN (2025) |
| **Rahma Attaya Tamimah** | 241511088 | D3 Teknik Informatika - POLBAN (2025) |
| **Zahra Aldila** | 241511094 | D3 Teknik Informatika - POLBAN (2025) |

---

## Fitur Utama Aplikasi (Role-Based)

SiDoRa beroperasi dengan sistem hak akses berbasis role (Admin, TU, Dosen, Kaprodi) yang memastikan keamanan dan kerahasiaan dokumen:

### 1. Autentikasi dan Manajemen Pengguna
- Login menggunakan email dan password.
- Pengelolaan akun oleh admin, penentuan role pengguna, dan pengamanan akses ke sistem.

### 2. Upload dan Pengelolaan Dokumen
- **Dosen:** Mengunggah dokumen pribadi (RPS, BKD, SKP, Bukti Pengajaran, Portofolio).
- **Tata Usaha (TU):** Mengunggah dokumen administratif (SK, Surat Tugas).

### 3. Manajemen Hak Akses (Access Control)
- Dosen dapat memberikan izin baca kepada TU atau Kaprodi.
- TU dapat mendistribusikan dokumen ke dosen tertentu.
- Sistem mencatat pemberi dan penerima hak akses.

### 4. Versioning Dokumen
- Setiap revisi yang diunggah akan otomatis membuat versi baru (v1, v2, dst) tanpa menghapus dokumen lama, menjaga histori lengkap aktivitas administrasi.

### 5. Portofolio & Integrasi PDDikti
- Memungkinkan dosen menghubungkan akun SiDoRa dengan data PDDikti via *Web Scraping* / API untuk otomatis menarik biodata, riwayat pendidikan, dan statistik Tridharma.

### 6. Evaluasi oleh Kaprodi & Komentar
- Kaprodi dapat me-review dokumen dosen, memberikan feedback, dan mengubah status dokumen menjadi (Valid, Perlu Revisi, Ditolak).

### 7. Dashboard Interaktif
- Tampilan dashboard berbeda menyesuaikan role (statistik RPS/BKD untuk dosen, dokumen validasi untuk kaprodi, monitoring untuk TU, dan log sistem untuk admin).

---

## Stack & Tools yang Digunakan

- **Frontend:** Vue 3 + Tailwind CSS + Vite
- **Backend:** Laravel
- **Database:** PostgreSQL / MySQL
- **Version Control:** Git + GitHub (Organisasi)
- **Deployment:** Vercel / VPS

---

## Struktur Branch Git

Semua anggota bekerja **dalam satu repository organisasi**, dengan pembagian branch sebagai berikut:

```text
main         → Versi stabil (Production / Release)
dev          → Integrasi semua fitur (Staging / Testing)
│
├── feature/  → Branch untuk pengembangan fitur masing-masing
│   ├── feature/ui-template
│   ├── feature/auth
│   └── feature/[nama-fitur-lainnya]
│
├── bugfix/   → Branch untuk perbaikan bug non-kritis
│   └── bugfix/[nama-bug]
│
└── hotfix/   → Branch untuk perbaikan bug di production (langsung ke main)
```

**Aturan Penamaan Commit:**
- `feat: [nama fitur]` untuk penambahan fitur baru
- `fix: [masalah]` untuk perbaikan bug
- `ui: [perubahan UI]` untuk perubahan desain atau styling
- `refactor: [kode]` untuk perbaikan kode tanpa menambah/mengubah fungsionalitas
- `docs: [dokumen]` untuk perubahan dokumentasi

---

## Struktur Direktori Proyek

Karena proyek ini menggunakan ekosistem Laravel + Vue + Vite, berikut adalah panduan singkat mengenai struktur folder utamanya:

- `/app` - Logic backend Laravel (Controllers, Models, Middleware).
- `/routes` - Definisi rute aplikasi (`web.php`, `api.php`).
- `/resources` - File Frontend mentah:
  - `/resources/js` - Source code Vue (Komponen, App.vue).
  - `/resources/css` - File CSS utama (Tailwind).
  - `/resources/views` - File Blade template (Entry point Vue).
- `/public` - File aset publik (gambar, file terkompilasi dari Vite).
- `/database` - File migrations, seeders, dan factories untuk struktur dan dummy database.
- `/config` - Konfigurasi aplikasi Laravel.
- `/tests` - Untuk testing fungsionalitas aplikasi backend maupun frontend.
- `/devops` - File terkait konfigurasi deployment atau environment.
- `.vite` / `node_modules` / `vendor` - Folder dependencies (jangan di-commit).

---

## Panduan Instalasi & Menjalankan Proyek (Lokal)

**1. Clone Repository**
```bash
git clone <URL_REPOSITORY>
cd devOps
```

**2. Install Dependencies**
Install dependency PHP (Laravel) dan Node.js (Vue/Tailwind):
```bash
composer install
npm install
```

**3. Setup Environment Variable**
Copy file `.env.example` menjadi `.env`, lalu atur koneksi database:
```bash
cp .env.example .env
```
Sesuaikan bagian database di dalam file `.env`:
```env
DB_CONNECTION=pgsql # atau mysql
DB_HOST=127.0.0.1
DB_PORT=5432        # atau 3306 untuk mysql
DB_DATABASE=nama_database_lokal
DB_USERNAME=username_db
DB_PASSWORD=password_db
```

**4. Generate Application Key**
```bash
php artisan key:generate
```

**5. Jalankan Migrasi Database**
```bash
php artisan migrate
```
*(Opsional: jalankan seeder jika ada data master/dummy awal)*
```bash
php artisan migrate --seed
```

**6. Jalankan Server Development**
Anda perlu menjalankan server Laravel dan Vite secara bersamaan (disarankan di tab terminal yang berbeda):

Terminal 1 (Backend - Laravel):
```bash
php artisan serve
```

Terminal 2 (Frontend - Vue/Vite):
```bash
npm run dev
```

Aplikasi sekarang dapat diakses melalui browser pada `http://localhost:8000`.
