<<<<<<< HEAD
# Sistem Inventaris Himpunan Mahasiswa Informatika (HMIF)

Aplikasi inventarisasi aset digital dan fisik khusus untuk Himpunan Mahasiswa Informatika (HMIF) yang dibangun menggunakan Framework **Laravel 10** (PHP ^8.1) berbasis arsitektur **MVC** dan pemrograman berorientasi objek (**OOP**).

---

## 🛠️ Fitur Utama
* **CRUD Data Barang**: Manajemen lengkap data barang digital/fisik inventaris himpunan.
* **Peminjaman & Pengembalian Barang**: Anggota dapat meminjam barang dan mengembalikannya secara mandiri (mengurangi stok otomatis saat dipinjam, menambah kembali saat dikembalikan).
* **Cetak QR Code**: Penomoran barang digital otomatis beserta generate QR Code dinamis untuk scan verifikasi barang.
* **Manajemen Pengguna (Role-based)**: Pembagian akses teratur untuk Administrator, Ketua Himpunan, Pengurus (Wakahim/Sekre/Bendahara/Staff), dan Anggota.
* **Export & Import Excel**: Mempermudah administrasi rekap barang dalam jumlah banyak.
* **Cetak PDF**: Membuat cetak kartu inventaris fisik.

---

## 🏗️ Letak Struktur MVC (Model-View-Controller)

Aplikasi ini menggunakan pola arsitektur **Model-View-Controller (MVC)** standar Laravel untuk memisahkan logika bisnis, penyimpanan data, dan tampilan antarmuka:

### 1. Model (M)
Bertanggung jawab atas pengelolaan data, validasi model, casts tipe data, dan relasi tabel database.
* **[Commodity.php](file:///d:/Kuliah/Proyek/inven-bs/app/Commodity.php)**: Mengatur data barang dan method bantuan format rupiah/tanggal.
* **[Loan.php](file:///d:/Kuliah/Proyek/inven-bs/app/Loan.php)**: Mengatur relasi peminjaman barang ke `User` (peminjam) dan `Commodity` (barang yang dipinjam).
* **[User.php](file:///d:/Kuliah/Proyek/inven-bs/app/User.php)**: Mengatur data otentikasi akun pengguna dan integrasi hak akses Spatie.

### 2. View (V)
Menangani visualisasi antarmuka pengguna (User Interface) menggunakan Blade template engine.
* **Folder [resources/views/commodities/](file:///d:/Kuliah/Proyek/inven-bs/resources/views/commodities)**: Halaman daftar barang, form modal tambah/ubah barang, serta template cetak PDF.
* **Folder [resources/views/loans/](file:///d:/Kuliah/Proyek/inven-bs/resources/views/loans)**: Halaman data peminjaman dan aksi pengembalian barang bagi anggota.
* **Folder [resources/views/components/](file:///d:/Kuliah/Proyek/inven-bs/resources/views/components)**: Komponen layout sidebar, navbar, filter, dan tabel yang *reusable*.

### 3. Controller (C)
Bertindak sebagai jembatan yang memproses *request* dari pengguna, berinteraksi dengan Model, dan mengembalikan hasil olahan data ke View.
* **[CommodityController.php](file:///d:/Kuliah/Proyek/inven-bs/app/Http/Controllers/CommodityController.php)**: Mengatur alur CRUD barang, cetak PDF, dan impor/ekspor data.
* **[LoanController.php](file:///d:/Kuliah/Proyek/inven-bs/app/Http/Controllers/LoanController.php)**: Mengontrol transaksi peminjaman barang dan pengembalian stok barang.
* **[HomeController.php](file:///d:/Kuliah/Proyek/inven-bs/app/Http/Controllers/HomeController.php)**: Mengontrol render dashboard statistik grafik kondisi barang, merk, dan material.

---

## 🧩 Letak Penerapan OOP (Object-Oriented Programming)

Konsep Pemrograman Berorientasi Objek diterapkan secara nyata di dalam kode program aplikasi ini:

### 1. Inheritance (Pewarisan)
Pewarisan digunakan untuk mewarisi sifat dan fungsionalitas dari kelas induk (*parent class*).
* **Controller**: Kelas [CommodityController](file:///d:/Kuliah/Proyek/inven-bs/app/Http/Controllers/CommodityController.php#L18) mewarisi kelas [Controller](file:///d:/Kuliah/Proyek/inven-bs/app/Http/Controllers/Controller.php) bawaan Laravel untuk mengakses fungsionalitas authorization dan middleware.
* **Model**: Kelas [Commodity](file:///d:/Kuliah/Proyek/inven-bs/app/Commodity.php#L10) mewarisi kelas `Eloquent\Model` untuk mendapatkan kemampuan manipulasi database active record.
* **FormRequest**: Kelas [StoreCommodityRequest](file:///d:/Kuliah/Proyek/inven-bs/app/Http/Requests/StoreCommodityRequest.php#L7) mewarisi `FormRequest` untuk validasi otomatis inputan form.

### 2. Encapsulation (Enkapsulasi)
Membungkus properti dan metode di dalam kelas untuk menjaga keamanan data dengan membatasi hak akses menggunakan visibilitas (`public`, `protected`, `private`).
* **[CommodityRepository.php](file:///d:/Kuliah/Proyek/inven-bs/app/Repositories/CommodityRepository.php#L9-L11)**: Menggunakan kata kunci `private` pada parameter constructor untuk mengamankan instance model agar hanya bisa diakses di dalam repositori tersebut:
  ```php
  public function __construct(
      private Commodity $model
  ) {}
  ```
* **[Commodity.php](file:///d:/Kuliah/Proyek/inven-bs/app/Commodity.php#L14-L18)**: Menggunakan visibilitas `protected` pada properti `$guarded` dan `$casts` untuk memproteksi konfigurasi internal model.

### 3. Abstraction (Abstraksi)
Menyembunyikan detail implementasi query database yang kompleks di belakang interface/kelas khusus.
* **Repository Pattern**: Diimplementasikan pada [CommodityRepository.php](file:///d:/Kuliah/Proyek/inven-bs/app/Repositories/CommodityRepository.php). Controller tidak perlu mengetahui bagaimana query database SQL ditulis (misal query grouping / sum), cukup memanggil metode abstraksi bersih seperti:
  ```php
  $this->commodityRepository->countCommodityCondition();
  ```

### 4. Dependency Injection & Polymorphism
* **Dependency Injection**: Memasukkan dependensi kelas yang dibutuhkan secara otomatis melalui parameter constructor. Terlihat pada [CommodityController.php](file:///d:/Kuliah/Proyek/inven-bs/app/Http/Controllers/CommodityController.php#L20-L24) di mana objek `CommodityRepository` diinjeksikan secara otomatis oleh service container Laravel.
* **Polimorfisme**: Terlihat pada validasi request di method `store(StoreCommodityRequest $request)` dan `update(UpdateCommodityRequest $request)`. Method controller dapat menerima tipe objek request yang berbeda namun diproses dengan interface penanganan request yang sama dari Laravel.

---

## 🚀 Langkah Instalasi & Uji Coba

1. **Clone repository ini**
   ```bash
   git clone https://github.com/mrizkimaulidan/inven-bs.git
   cd inven-bs
   ```

2. **Install dependensi PHP via Composer**
   ```bash
   composer install
   ```

3. **Konfigurasi Environment**
   * Salin file `.env.example` menjadi `.env`
   * Buat database baru bernama `inven-bs` (atau nama lain) di MySQL Anda (misalnya melalui phpMyAdmin atau terminal).
   * Buka berkas `.env` baru Anda dan sesuaikan detail database pada bagian:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=inven-bs
     DB_USERNAME=root
     DB_PASSWORD=
     ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Jalankan Database Migration & Seeding** (untuk membuat struktur tabel dan mengisi 545 akun dummy otomatis):
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **(Opsional) Install Dependensi Frontend & Kompilasi Aset** (jika ingin memodifikasi CSS/JS):
   ```bash
   npm install
   npm run dev
   ```

7. **Jalankan Web Server Lokal**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses di browser melalui tautan `http://127.0.0.1:8000`.

---

## 🔑 Kredensial Akun Dummy Pengujian (Password: `secret`)
* **Administrator**: `admin@mail.com`
* **Ketua Himpunan**: `kahim@mail.com`
* **Wakil Ketua Himpunan**: `wakahim@mail.com`
* **Bendahara**: `bendahara@mail.com`
* **Sekretaris**: `sekretaris@mail.com`
* **Staff Himpunan (1 - 40)**: `staff1@mail.com` s.d. `staff40@mail.com`
* **Anggota Himpunan (1 - 500)**: `anggota1@mail.com` s.d. `anggota500@mail.com`
=======
# Inventaris-hmit
>>>>>>> 43b3b749d2481ef7421eb1616028f133d870be08
