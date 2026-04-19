## Pembuat

Project ini dibuat oleh:

- **Muhammad Rizqullah Almadinah**
- **Aditri Surya Nugraha**

untuk mata kuliah **Pengembangan Aplikasi Web Lanjut – Kelas A**.

# Project Collab API

REST API backend untuk platform kolaborasi ide, dibangun dengan **Laravel** + **JWT Authentication**.

## Fitur Utama

- **Otentikasi dengan JWT (JSON Web Token)**: Register, Login, dan pengecekan pengguna yang sedang login (Get Current User).
- **Manajemen Ide / Proyek (Ideas)**: Pengguna dapat membuat (beserta upload gambar), melihat, mengubah, dan menghapus ide.
- **Sistem Kolaborasi (Join Requests)**: 
  - Pengguna dapat mengirimkan permintaan bergabung (*join request*) ke dalam ide proyek milik orang lain.
  - Pembuat ide dapat melihat dan mengelola request yang masuk.
- **Role-Based Access Control (RBAC)**:
  - **member**: Hanya dapat mengubah atau menghapus ide miliknya sendiri.
  - **admin**: Memiliki kewenangan penuh untuk mengubah atau menghapus ide milik siapa pun.
- **Standarisasi API**: Dilengkapi dengan validasi data *request* dan struktur *response* JSON yang konsisten.

---

## Tech Stack

- PHP 8.x
- Laravel 8/9 (sesuai project kamu)
- MySQL / MariaDB
- `tymon/jwt-auth` untuk token JWT
- Laravel Storage (public disk) untuk upload gambar

---

## Instalasi

1. Clone repository
2. Install dependency

```bash
composer install
```

3. Copy file environment

```bash
cp .env.example .env
```

> Jika di Windows:
```bash
copy .env.example .env
```

4. Generate app key

```bash
php artisan key:generate
```

5. Atur konfigurasi database di `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_collab_api
DB_USERNAME=root
DB_PASSWORD=
```

6. Jalankan migration

```bash
php artisan migrate
```

7. Install JWT & generate secret (jika belum)

```bash
composer require tymon/jwt-auth
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
```

8. Pastikan guard API pakai JWT di `config/auth.php`

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

9. Link storage untuk akses gambar

```bash
php artisan storage:link
```

10. Jalankan server

```bash
php artisan serve
```

Base URL default:
`http://127.0.0.1:8000/api`

---

## Authentication Flow

1. Register user
2. Login untuk mendapat `access_token`
3. Kirim token di header:

```http
Authorization: Bearer <access_token>
Accept: application/json
```

---

## Role

- `member` (default saat register)
- `admin`

Aturan di Ideas:
- member hanya bisa update/delete idea miliknya
- admin bisa update/delete idea siapa pun

---

## Endpoint List

## Auth

### Register
- **POST** `/api/register`
- Body (JSON):
```json
{
  "name": "Dafi",
  "email": "dafi@mail.com",
  "password": "password123"
}
```

### Login
- **POST** `/api/login`
- Body (JSON):
```json
{
  "email": "dafi@mail.com",
  "password": "password123"
}
```

### Me
- **GET** `/api/me`
- Header: Bearer token (required)

---

## Ideas

> Semua endpoint Ideas butuh Bearer token.

### Get all ideas
- **GET** `/api/ideas`

### Create idea
- **POST** `/api/ideas`
- Body: `form-data`
  - `title` (text, required)
  - `description` (text, required)
  - `image` (file, optional, jpg/jpeg/png, max 2MB)

### Get detail idea
- **GET** `/api/ideas/{id}`

### Update idea
- **PUT** `/api/ideas/{id}`
- Body: `form-data`
  - `title` (text, optional)
  - `description` (text, optional)
  - `image` (file, optional)

### Delete idea
- **DELETE** `/api/ideas/{id}`

---

## Join Requests

> Semua endpoint Requests butuh Bearer token.

### Get all requests
- **GET** `/api/requests`

### Create request
- **POST** `/api/requests`
- Body (JSON):
```json
{
  "idea_id": 1
}
```

### Get detail request
- **GET** `/api/requests/{id}`

### Update request status
- **PUT** `/api/requests/{id}`
- Body (JSON):
```json
{
  "status": "accepted"
}
```
Nilai status yang valid:
- `pending`
- `accepted`
- `rejected`

### Delete request
- **DELETE** `/api/requests/{id}`

---

## Struktur Relasi Data

- Satu `User` punya banyak `Idea`
- Satu `User` punya banyak `JoinRequest`
- Satu `Idea` punya banyak `JoinRequest`
- `join_requests` menghubungkan user dan idea (dengan status)

---

## Response Code Umum

- `200` OK
- `201` Created
- `401` Unauthorized (token tidak ada/invalid)
- `403` Forbidden (tidak punya hak akses)
- `404` Not Found
- `422` Validation Error

---

## Testing Manual (Postman)

Urutan yang disarankan:
1. Register user member
2. Login member → simpan token
3. Register/login admin → simpan token admin
4. Create idea dengan token member (upload image)
5. Coba update idea member pakai token admin (harus bisa)
6. Buat join request ke idea
7. Uji skenario error:
   - tanpa token (401)
   - bukan owner update idea (403)
   - upload file invalid (422)

---

## Troubleshooting

### Error JWT secret null
Contoh error:
`Argument 3 passed to ... Key, null given`

Solusi:
```bash
php artisan jwt:secret
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan serve
```
Lalu login ulang untuk token baru.

### Route Not Found
```bash
php artisan route:list
php artisan route:clear
php artisan optimize:clear
```
Pastikan URL memakai prefix `/api`.

---

## Catatan Keamanan

- Password di-hash (`bcrypt/Hash::make`)
- Jangan expose `.env` ke repository publik
- Batasi ukuran dan tipe file upload
- Gunakan HTTPS di production

---

## Pengembangan Lanjutan (Opsional)

- Gunakan Laravel Policy untuk authorization
- Tambahkan pagination (`paginate()`) di list endpoint
- Standarisasi response format (resource class)
- Tambahkan unit/integration test (PHPUnit)
- Tambahkan refresh token / token blacklist strategy

---

## License

Project ini untuk pembelajaran & pengembangan internal.