# Flash Sale App

Repository ini berisi dua task:

- **task-1-api**: REST API flash sale berbasis Laravel (auth, produk, order)
- **task-2-cli**: game CLI PHP sederhana (`hidden_item.php`)

## Struktur

```text
flash-sale-app/
├── task-1-api/
└── task-2-cli/
```

## Menjalankan Task 1 (API Laravel)

```bash
cd /home/runner/work/flash-sale-app/flash-sale-app/task-1-api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

API utama ada di `/api`:

- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/logout` (auth)
- `GET/POST/PUT/DELETE /api/products` (auth)
- `PATCH /api/products/{product}/restore` (auth)
- `DELETE /api/products/{product}/delete-permanent` (auth)
- `POST /api/orders` (auth)

Menjalankan test:

```bash
cd /home/runner/work/flash-sale-app/flash-sale-app/task-1-api
php artisan test
```

## Menjalankan Task 2 (CLI)

```bash
php /home/runner/work/flash-sale-app/flash-sale-app/task-2-cli/hidden_item.php
```

Lalu masukkan nilai langkah A (utara), B (timur), dan C (selatan) saat diminta.
