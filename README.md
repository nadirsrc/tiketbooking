# Sistem Pemesanan Tiket Event (Ticket Box)

Aplikasi web berbasis Laravel yang dirancang untuk mengelola katalog tiket event, memfasilitasi transaksi pemesanan tiket konser, festival, dan seminar oleh publik, serta menyediakan modul back-office terintegrasi untuk kebutuhan administrasi data dan rekapitulasi pesanan.

---

## Daftar Isi
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Fitur Utama Sistem](#fitur-utama-sistem)
- [Struktur Basis Data](#struktur-basis-data)
- [Prasyarat Sistem](#prasyarat-sistem)
- [Instalasi dan Konfigurasi](#instalasi-dan-konfigurasi)
- [Kredensial Pengujian](#kredensial-pengujian)
- [Lisensi](#lisensi)

---

## Teknologi yang Digunakan

- **Framework Back-End**: [Laravel 13](https://laravel.com/)
- **Bahasa Pemrograman**: PHP >= 8.5
- **Basis Data**: MySQL
- **Front-End**: Blade Template Engine, Tailwind CSS, JavaScript Vanilla
- **Autentikasi**: Laravel Breeze
- **Manajemen Ketergantungan**: Composer & NPM

---

## Fitur Utama Sistem

### 1. Antarmuka Pelanggan (Customer / Public)
- **Katalog Tiket Event**: Menampilkan daftar tiket event aktif yang dilengkapi poster, kategori, deskripsi singkat, dan harga satuan.
- **Filter Kategori Dinamis**: Penyaringan kategori event (Musik, Festival, Seminar) secara instan di sisi klien tanpa perlu memuat ulang (*reload*) halaman.
- **Formulir Pemesanan Multi-Item**: Pemesan dapat memilih jumlah kuantitas tiket pada beberapa event sekaligus dalam satu kali transaksi.
- **Integritas Transaksi Atomik (ACID)**: Penerapan `DB::beginTransaction()` untuk menjamin bahwa data header pesanan (`orders`) dan rincian item (`order_details`) tersimpan secara utuh, atau dibatalkan otomatis (*rollback*) apabila terjadi kendala sistem.

### 2. Antarmuka Administrator (Back-Office)
- **Autentikasi Terproteksi**: Akses modul administrasi dibatasi melalui middleware autentikasi sesi.
- **Manajemen Master Tiket (CRUD Event)**:
  - Pencatatan event baru beserta harga dan kuota informasi.
  - Unggah poster gambar dengan validasi format berkas (JPEG, PNG, JPG) dan batas ukuran maksimal 2MB.
  - Pembaruan data tiket dan pembersihan otomatis berkas gambar lama dari penyimpanan server saat diganti.
  - Penghapusan data tiket yang terhubung secara berjenjang (*cascade delete*) ke relasi rincian pesanan.
- **Dashboard Rekapitulasi Pesanan**:
  - Monitoring transaksi masuk secara berkala (*real-time view*).
  - Optimasi query basis data menggunakan *Eager Loading* untuk mengeliminasi masalah *N+1 Query Problem*.
  - Rincian komprehensif: ID pesanan, nama pemesan, email, rincian tiket beserta subtotal, serta total akumulasi biaya.
  - Kontrol status transaksi interaktif (*Pending*, *Confirmed*, *Cancelled*).

---

## Struktur Basis Data

Entitas utama yang membangun alur kerja aplikasi meliputi:

1. **`events`**  
   Menyimpan data master tiket acara.  
   *Kolom*: `id`, `title`, `category` (enum: Musik, Festival, Seminar), `ticket_price`, `description`, `image`, `timestamps`.

2. **`orders`**  
   Menyimpan header data pemesanan pelanggan.  
   *Kolom*: `id`, `customer_name`, `customer_email`, `total_price`, `status` (enum: Pending, Confirmed, Cancelled), `timestamps`.

3. **`order_details`**  
   Menyimpan rincian setiap tiket yang dipesan dalam satu transaksi pesanan.  
   *Kolom*: `id`, `order_id` (foreign key -> orders), `event_id` (foreign key -> events), `quantity`, `subtotal`, `timestamps`.

4. **`users`**  
   Menyimpan akun pengguna untuk otentikasi administrator ke dashboard back-office.

---

## Prasyarat Sistem

Sebelum menjalankan proyek di lingkungan lokal, pastikan perangkat telah terpasang perangkat lunak berikut:

- PHP versi 8.5 atau yang lebih tinggi (beserta ekstensi pdo_mysql, mbstring, openssl, gd/fileinfo)
- Composer versi 2.x
- Node.js (versi LTS disarankan) dan NPM
- MySQL / MariaDB Server

---

## Instalasi dan Konfigurasi

Ikuti langkah-langkah berikut untuk menyiapkan aplikasi di lingkungan lokal:

1. **Kloning Repositori**:
   ```bash
   git clone https://github.com/nadirsrc/tiketbooking.git
   cd tiketbooking
   ```

2. **Instalasi Dependensi PHP**:
   ```bash
   composer install
   ```

3. **Instalasi Dependensi Front-End**:
   ```bash
   npm install
   ```

4. **Konfigurasi Berkas Lingkungan (`.env`)**:
   Salin berkas template lingkungan dan buat file `.env`:
   ```bash
   cp .env.example .env
   ```
   Buka berkas `.env` dan sesuaikan parameter koneksi basis data:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_ticket_konser
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Generate Kunci Aplikasi**:
   ```bash
   php artisan key:generate
   ```

6. **Migrasi Basis Data dan Seeding**:
   Jalankan migrasi tabel beserta data awal (*dummy events & akun admin*):
   ```bash
   php artisan migrate --seed
   ```

7. **Pembuatan Tautan Media Penyimpanan (*Storage Link*)**:
   Buat symbolic link dari folder storage publik ke direktori public web:
   ```bash
   php artisan storage:link
   ```

8. **Kompilasi Aset dan Menjalankan Server Pengembangan**:
   Jalankan server aplikasi Laravel:
   ```bash
   php artisan serve
   ```
   Di jendela terminal terpisah, jalankan bundling aset Tailwind/Vite:
   ```bash
   npm run dev
   ```

Aplikasi dapat diakses melalui peramban web pada alamat: `http://127.0.0.1:8000`

---

## Kredensial Pengujian

Gunakan akun administrator bawaan hasil seeding untuk mengakses dashboard manajemen:

- **URL Login**: `http://127.0.0.1:8000/login`
- **Email**: `Admin@gmail.com`
- **Password**: `password123`

---

## Lisensi

Proyek ini dikembangkan menggunakan lisensi terbuka [MIT License](LICENSE).
