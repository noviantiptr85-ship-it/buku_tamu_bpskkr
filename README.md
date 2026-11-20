# Buku Tamu Digital — BPS Kabupaten Kubu Raya

Aplikasi buku tamu berbasis PHP native + MariaDB (XAMPP). Tamu mengisi formulir di tablet kiosk, petugas memantau dan mencatat check-out, pimpinan melihat laporan.

## Fitur

- **Kiosk**: slideshow 9 infografis (autoplay, swipe, dot navigation), tombol **Isi Buku Tamu**.
- **Form 4 kolom wajib**: nama, instansi/asal, nomor HP, keterangan.
- **Layar sukses 5 detik** dengan nomor kunjungan `BT-YYYYMMDD-NNN`, lalu kembali otomatis ke slideshow.
- **Dashboard petugas**: daftar tamu, pencarian, filter tanggal/status, check-out, tambah tamu manual.
- **Laporan**: ringkasan, 5 instansi terbanyak, export CSV/Excel, cetak PDF (print browser).
- **Admin**: kelola infografis (upload/urutkan/sembunyikan/hapus), pengaturan branding & durasi, pengguna, audit log.
- **Peran**: admin, petugas, pimpinan (read-only).
- **Keamanan**: PDO prepared statement, CSRF token, password hash, session httpOnly, role-based access.

## Kebutuhan

- XAMPP (Apache + MariaDB) — sudah terpasang di `C:\xampp`
- PHP 8.2+ (teruji di PHP 8.5.12)
- Database `buku_tamu` (sudah dibuat)

## Struktur Folder

```
buku tamu/
├─ app/
│  ├─ config.php          # pembaca .env, konstanta, session
│  ├─ db.php              # koneksi PDO
│  ├─ helpers.php         # escape, CSRF, format, render view
│  ├─ validation.php      # validasi form buku tamu
│  ├─ auth.php            # login, role, session
│  ├─ models/             # User, Visit, Infographic, Setting, AuditLog
│  └─ controllers/        # kiosk, auth, dashboard, reports, admin
├─ views/                 # layout + halaman
├─ public/                # document root
│  ├─ index.php           # router
│  ├─ assets/css|js|img
│  └─ assets/infografis/  # 9 gambar hasil seed
├─ database/
│  ├─ schema.sql          # skema tabel
│  └─ seed.php            # 3 user + 9 slide + settings
├─ storage/               # log/backup (di luar public)
├─ .env
├─ serve.bat
└─ README.md
```

## Menjalankan

1. Pastikan **MySQL** (dan Apache bila ingin phpMyAdmin) berjalan di XAMPP Control Panel.
2. Dari folder proyek, jalankan seeder sekali:

   ```bash
   C:\xampp\php\php.exe database\seed.php
   ```

3. Jalankan server:

   ```bash
   C:\xampp\php\php.exe -S localhost:8000 -t public public/index.php
   ```

   Atau klik dua kali `serve.bat`.

4. Buka:
   - Kiosk: <http://localhost:8000>
   - Login petugas/admin: <http://localhost:8000/login>

> Catatan: `-t public` penting agar folder `app/`, `.env`, dan `database/` tidak dapat diakses dari browser.

## Akun Demo

| Peran | Email | Password |
|---|---|---|
| Admin | `admin@bpskuburaya.local` | `Admin#BPS2026` |
| Petugas | `petugas@bpskuburaya.local` | `Petugas#BPS2026` |
| Pimpinan | `pimpinan@bpskuburaya.local` | `Pimpinan#BPS2026` |

**Ganti password sebelum dipakai produksi** (menu Pengguna).

## Halaman

| URL | Akses | Keterangan |
|---|---|---|
| `/` | Publik | Kiosk: slideshow + tombol |
| `/isi` | Publik | Form buku tamu |
| `/sukses` | Publik | Layar sukses (5 detik) |
| `/login` | Publik | Login petugas/admin/pimpinan |
| `/dashboard` | Login | Daftar tamu + check-out |
| `/dashboard/tambah` | Admin/Petugas | Input tamu manual |
| `/laporan` | Login | Laporan + export |
| `/admin/slides` | Admin | Kelola infografis |
| `/admin/pengaturan` | Admin | Branding & durasi |
| `/admin/users` | Admin | Kelola pengguna |
| `/admin/audit` | Admin | Audit log |

## Pengaturan

Menu **Admin → Pengaturan**:

- `site_name` — nama instansi
- `welcome_text` — teks di bawah tombol kiosk
- `running_text` — teks berjalan (opsional)
- `slide_duration` — detik per slide (3–15, default 5)
- `success_duration` — detik layar sukses (3–15, default 5)

## Backup & Restore

Backup database:

```bash
C:\xampp\mysql\bin\mariadb-dump.exe -u root buku_tamu > backup-buku-tamu.sql
```

Restore:

```bash
C:\xampp\mysql\bin\mysql.exe -u root buku_tamu < backup-buku-tamu.sql
```

Folder gambar: `public/assets/infografis/` — ikut dicadangkan.

## Sebelum Produksi

1. Ganti semua password akun demo.
2. Buat user database khusus (jangan `root` tanpa password) dan isi di `.env`.
3. Set `APP_ENV=production` di `.env` agar detail error tidak tampil.
4. Pasang HTTPS (Apache + sertifikat, atau reverse proxy).
5. Backup harian otomatis (`mariadb-dump` + salin folder gambar).
6. Kunci tablet kiosk memakai mode kiosk browser.

## Troubleshooting

| Masalah | Solusi |
|---|---|
| `Koneksi database gagal` | Pastikan MySQL XAMPP berjalan dan `DB_*` di `.env` benar |
| Halaman 404 di `/isi` | Jalankan dengan `-t public public/index.php` (lihat perintah di atas) |
| Gambar tidak muncul | Pastikan `public/assets/infografis/` berisi 9 file; jalankan ulang `seed.php` |
| Sesi formulir kedaluwarsa (419) | Muat ulang halaman, lalu isi ulang form |
| Login gagal berulang | Tunggu 60 detik (ada pembatasan 5 percobaan) |
