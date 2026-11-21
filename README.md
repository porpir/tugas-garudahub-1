# Garuda-Hub (Refactored to Laravel MVC)

Aplikasi lelang proyek IT berbasis web yang telah di-refactor dari PHP Native (*spaghetti code*) menjadi arsitektur **MVC** menggunakan framework **Laravel** dan **Eloquent ORM**.

## Fitur Utama
- **Autentikasi & Multi-Role**: Registrasi dan login untuk peran `klien`, `developer`, dan `admin`.
- **Manajemen Proyek (CRUD)**: Klien dapat memasang proyek lelang lengkap dengan rentang budget dan batas waktu lelang.
- **Sistem Penawaran (Bidding)**: Developer dapat mengajukan penawaran harga, durasi pengerjaan, dan proposal pada proyek yang masih terbuka.
- **Pemilihan Pemenang**: Klien dapat memilih satu penawaran pemenang yang otomatis menutup status lelang proyek.
- **Dasbor & Panel Admin**: Dasbor khusus sesuai peran pengguna serta panel admin untuk mengelola pengguna dan proyek.

## Skema Relasional Database
- `users`: Menyimpan data pengguna beserta perannya (`klien`, `developer`, `admin`).
- `projects`: Menyimpan data proyek lelang dengan *foreign key* `client_id` merujuk ke `users.id`.
- `bids`: Menyimpan data penawaran dengan *foreign key* `project_id` (`projects.id`) dan `developer_id` (`users.id`), serta *unique constraint* pada kombinasi `(project_id, developer_id)`.

## Cara Menjalankan Lokal
1. Salin `.env.example` menjadi `.env` dan sesuaikan pengaturan database MySQL (`DB_DATABASE=garuda_hub_laravel`).
2. Jalankan migrasi dan data dummy:
   ```bash
   php artisan migrate --seed