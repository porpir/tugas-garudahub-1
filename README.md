# Garuda-Hub (Refactored to Laravel MVC)

Aplikasi lelang proyek IT berbasis web yang telah di-refactor dari PHP Native (*spaghetti code*) menjadi arsitektur **MVC** menggunakan framework **Laravel** dan **Eloquent ORM**.

## Fitur Utama
- **Autentikasi & Multi-Role**: Registrasi dan login untuk peran `klien`, `developer`, dan `admin` menggunakan hashing password bawaan Laravel.
- **Manajemen Proyek (Full CRUD)**: Klien dapat membuat, melihat, mengedit, dan menghapus proyek lelang selama status lelang masih `terbuka`.
- **Sistem Penawaran (Bidding)**: Developer dapat mengajukan penawaran harga, durasi pengerjaan, dan proposal pada proyek yang masih terbuka.
- **Pemilihan Pemenang**: Klien pemilik proyek dapat memilih satu penawaran pemenang yang otomatis menutup status lelang proyek.
- **Dasbor & Panel Admin**: Dasbor khusus sesuai peran pengguna serta panel admin untuk mengelola peran/akun pengguna dan menghapus proyek.

## Arsitektur & Relasi Database (ERD)
```text
+------------------+       1:N       +-------------------+       1:N       +--------------------+
|      users       |---------------->|     projects      |---------------->|        bids        |
+------------------+                 +-------------------+                 +--------------------+
| id (PK)          |                 | id (PK)           |                 | id (PK)            |
| name             |                 | client_id (FK)    |                 | project_id (FK)    |
| email (UNIQUE)   |                 | judul             |                 | developer_id (FK)  |
| password         |                 | deskripsi         |                 | harga              |
| role (enum)      |                 | kategori          |                 | durasi             |
+------------------+                 | budget_min        |                 | proposal           |
         |                           | budget_max        |                 | is_winner (bool)   |
         |                           | batas_lelang      |                 +--------------------+
         |                           | status (enum)     |                           ^
         +---------------------------------------------------------------------------+
                                              1:N (developer_id)