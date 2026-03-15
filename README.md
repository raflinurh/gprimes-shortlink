# GPrimes Shortlink (Private Tool)

Sistem shortlink internal dan bersifat privat berbasis PHP tanpa database (menggunakan JSON). Akses admin dilindungi password.

## Demo
- **URL Demo:** [demo-shortlink.gprimes.net](https://demo-shortlink.gprimes.net)
- **Password Admin:** `admin123`

## Fitur
- Custom slug untuk setiap link.
- Statistik jumlah klik (hits).
- Panel Admin untuk kelola link (buat, edit, hapus).
- Validasi cek link ganda sebelum simpan.
- Tampilan responsif & modern.

## Struktur File
- `index.php`: Halaman depan & sistem redirect.
- `admin.php`: Panel dashboard admin.
- `data/links.json`: Tempat penyimpanan data link.
- `.htaccess`: Pengaturan URL agar lebih bersih.

## Cara Penggunaan
1. Upload semua file ke hosting/server.
2. Edit password admin di `admin.php` pada baris `$password = "admin123";`.
3. Buka `domain.com/admin.php` untuk mulai membuat link.
