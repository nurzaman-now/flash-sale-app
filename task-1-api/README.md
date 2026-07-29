# Task 1 - Flash Sale REST API (Laravel)

API toko online sederhana yang dibuat dengan **Laravel** untuk menangani transaksi pemesanan barang. Proyek ini dilengkapi dengan mekanisme pencegahan *race condition* (stok minus/overselling) pada fitur *flash sale* menggunakan teknik database row locking (`lockForUpdate`).

---

## 🛠️ Fitur Utama

- **Autentikasi (Laravel Sanctum)**: Fitur pendaftaran, masuk, dan keluar pengguna menggunakan token Bearer.
- **Manajemen Produk**: CRUD produk lengkap dengan fitur pencarian (*search*), paginasi, serta *soft deletes*.
- **Pemesanan & Flash Sale**:
  - Pembuatan pesanan (*order*) beserta item detailnya (`order_items`).
  - Pengurangan stok produk secara aman dari kendala *race condition* saat diakses secara bersamaan (konkuren).
- **Format Respon API Seragam**: Penanganan error dan format JSON respon yang konsisten di seluruh endpoint API.

---

## 🚀 Panduan Instalasi & Penggunaan

### 1. Prasyarat
- PHP >= 8.0
- Composer
- Database Server (MySQL / MariaDB)

### 2. Langkah Instalasi

```bash
# Masuk ke direktori task 1
cd task-1-api

# Install dependensi
composer install

# Salin file konfigurasi .env
cp .env.example .env

# Generate application key
php artisan key:generate
```

Sesuaikan konfigurasi database pada file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=flash_sale_db
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migrasi database:
```bash
php artisan migrate
```

Jalankan server lokal:
```bash
php artisan serve
```
Aplikasi akan berjalan di `http://127.0.0.1:8000`.

---

## 📌 Daftar Endpoint API

### Autentikasi
- `POST /api/auth/register` - Pendaftaran pengguna baru
- `POST /api/auth/login` - Masuk dan mendapatkan token
- `POST /api/auth/logout` - Keluar (memerlukan Auth Token)

### Produk (Memerlukan Auth Token)
- `GET /api/products` - Mengambil daftar produk (dukungan `search` & `page`)
- `POST /api/products` - Menambahkan produk baru
- `GET /api/products/trashed` - Mengambil daftar produk yang terhapus
- `GET /api/products/{id}` - Menampilkan detail produk
- `PUT /api/products/{id}` - Mengubah data produk
- `DELETE /api/products/{id}` - Menghapus produk (*soft delete*)
- `PATCH /api/products/{id}/restore` - Mengembalikan produk yang terhapus
- `DELETE /api/products/{id}/delete-permanent` - Menghapus produk secara permanen

### Pemesanan (Memerlukan Auth Token)
- `POST /api/orders` - Membuat pesanan baru (*flash sale*)

---

## 🧪 Pengujian Race Condition

Untuk membuktikan bahwa sistem tahan terhadap kondisi persaingan (*race condition*), telah disediakan custom Artisan command:

```bash
php artisan test:race-condition
```
Command ini akan mensimulasikan 10 permintaan pemesanan secara bersamaan (konkuren) terhadap produk dengan stok terbatas.
