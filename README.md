# TUMBUH — Platform Pemantauan Pertumbuhan Balita & Gizi Posyandu

**Tumbuh** adalah platform web terpadu untuk kader Posyandu dan tenaga kesehatan komunitas yang mendukung pencapaian **SDG 2 (Zero Hunger)** dan **SDG 3 (Good Health and Well-Being)**. Platform ini mengotomatiskan kalkulasi Z-Score WHO untuk deteksi dini stunting, menampilkan kurva digital KMS interaktif, menyediakan rekomendasi menu lokal terjangkau melalui **AI Clinical Nutrition Advisor**, pusat edukasi nutrisi **Tumbuh Academy**, serta portal pantau bebas password via token unik & pesan WhatsApp untuk ibu/orang tua balita.

---

## 🛠️ Tech Stack & Arsitektur

- **Backend:** Laravel 13 (PHP 8.3+)
- **Database:** PostgreSQL (Database: `tumbuh`)
- **Frontend:** Laravel Blade, Tailwind CSS, Alpine.js, Chart.js, Lucide Icons
- **Tipografi:** Google Font `'Roboto'`
- **Desain UI/UX:** Clean, utilitarian, anti-AI slop, data density tinggi dengan palet warna Lime (`#65A30D`)
- **Engine Pertumbuhan:** Standard Baku Antropometri Anak WHO 2006 (0–60 Bulan)
- **AI Advisor:** Gemini API / OpenAI API dengan fallback klinis berstandar Kemenkes RI

---

## 🚀 Fitur Utama

1. **Dashboard Kader & Triase Klinis:**
   - Ringkasan kasus stunting, wasting, dan BB stagnan (2T).
   - Indikator peringatan dini untuk anak yang memerlukan intervensi segera.
2. **Pencatatan Penimbangan Cepat (Batch Entry):**
   - Mode input massal untuk hari Posyandu yang padat.
   - Perhitungan langsung dan live preview badge status gizi (Hijau/Kuning/Merah) secara instan.
3. **KMS Digital Interaktif (Chart.js):**
   - Plotting data historis pertumbuhan anak pada kurva baku WHO (0–60 bulan).
   - Garis referensi: +2 SD, Median 0 SD (#16A34A), -2 SD (#D97706), -3 SD (#DC2626).
   - Toggle instan antara Tinggi Badan (TB/U) dan Berat Badan (BB/U).
4. **AI Clinical Nutrition Advisor:**
   - Slider anggaran belanja makan harian (Rp10.000 – Rp50.000).
   - Rekomendasi menu pangan lokal terjangkau (telur, hati ayam, tempe, tahu, lele, ikan kembung).
   - Struktur rekomendasi 3-part: Diagnosis Klinis, Menu Harian, Panduan Pola Asuh.
5. **Portal Orang Tua Bebas Password (`/pantau/{token}`):**
   - Akses mobile-friendly bagi ibu tanpa perlu login wall.
   - Pelacak pencapaian 1.000 Hari Pertama Kehidupan (HPK).
   - Tombol otomatis *"Bagikan ke WhatsApp"* dengan format teks klinis siap kirim.
6. **Tumbuh Academy (`/edukasi`):**
   - Modul video YouTube dengan filter kategori (MPASI, ASI Eksklusif, Sanitasi/PHBS, Gizi Bumil).
   - Kuis pemahaman mandiri dengan evaluasi instan dan pembahasan klinis.
7. **Panel Administrator:**
   - Metrik agregat seluruh wilayah binaan.
   - Manajemen akun kader & tenaga medis.
   - Manajemen modul video edukasi dan data kuis JSON.
8. **Laporan Bulanan & Ekspor SKDN/SIP (`/kader/reports`):**
   - Rekapitulasi indikator resmi SKDN (Sasaran, KMS, Ditimbang, Naik BB, D/S %, N/D %) dan prevalensi stunting bulanan.
   - Ekspor data 1-klik ke spreadsheet Excel (.CSV dengan UTF-8 BOM).
   - Format cetak resmi berstandar A4 dengan kop Puskesmas dan kolom tanda tangan Bidan Desa & Ketua Kader.
9. **Buku Imunisasi Dasar Lengkap & Suplementasi Vitamin A:**
   - Pelacak status imunisasi rutin lengkap (HB0, BCG, Polio 1-4, DPT-HB-Hib 1-3, PCV 1-3, IPV, Campak-Rubela MR), Kapsul Vitamin A (Biru/Merah), dan obat cacing.
   - Terhubung langsung ke riwayat profil balita di dashboard kader serta dapat dipantau orang tua dari portal mobile.
10. **Cetak Kartu KMS Digital (`/kader/children/{id}/kms-print` & `/pantau/{token}/kms-print`):**
    - Lembar kartu KMS siap cetak (printer-friendly / Print to PDF) berisi riwayat biometrik dan imunisasi untuk keluarga yang membutuhkan fisik cetak.
11. **Kalkulator Z-Score Cepat Tanpa Login (`/kalkulator-gizi`):**
    - Skrining mandiri instan untuk publik dan kader lapangan dengan live preview kartu indikator WHO (TB/U, BB/U, BB/TB) dan peringatan triase klinis.
12. **Broadcast WhatsApp Pengingat Hari Posyandu (`/kader/broadcast-wa`):**
    - Pengirim pengingat jadwal penimbangan dan imunisasi berkala ke WhatsApp orang tua balita dengan satu sentuhan untuk meningkatkan cakupan D/S.

---

## 👤 Akun Demo & Kredensial Pengujian

| Peran (Role) | Email | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Kader Posyandu** | `kader1@tumbuh.id` | `password` | Posyandu Melati RW 03 |
| **Kader Posyandu** | `kader2@tumbuh.id` | `password` | Posyandu Melati RW 03 |
| **Administrator** | `admin@tumbuh.id` | `password` | dr. Hendra Pratama, Sp.A |

---

## 💻 Panduan Instalasi & Menjalankan Aplikasi

### 1. Konfigurasi Environment (`.env`)
```env
APP_NAME=Tumbuh
APP_ENV=local
APP_KEY=base64:eqqIrWN17MCCatQ0vcSugOMUr+i8xNGcyM1kbZQAD8I=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=tumbuh
DB_USERNAME=postgres
DB_PASSWORD=your_postgres_password

AI_PROVIDER=gemini # or openai
GEMINI_API_KEY=your_gemini_api_key
OPENAI_API_KEY=your_openai_api_key
```

### 2. Migrasi Database & Seeding
```bash
php artisan migrate:fresh --seed
```
*Seeder otomatis menginisialisasi akun Admin & Kader, balita dengan riwayat pengukuran, catatan imunisasi, serta modul video edukasi & kuis.*

### 3. Kompilasi Aset Frontend
```bash
npm run build
```

### 4. Menjalankan Server Lokal
```bash
php artisan serve
```
Akses web melalui browser di: `http://localhost:8000`

### 5. Menjalankan Automated Test
```bash
php artisan test
```
*Seluruh 25 pengujian unit dan fitur terverifikasi 100% lulus.*
