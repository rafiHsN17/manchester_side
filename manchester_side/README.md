# Manchester Side - Website Berita CRUD

Website berita untuk Manchester United dan Manchester City dengan fitur CRUD.

## Cara Install

1. **Jalankan XAMPP**
   - Start Apache
   - Start MySQL

2. **Import Database**
   - Buka phpMyAdmin: http://localhost/phpmyadmin
   - Klik "Import"
   - Pilih file `schema.sql`
   - Klik "Go"

3. **Akses Website**
   - Homepage: http://localhost/manchester_side/
   - Admin: http://localhost/manchester_side/admin/login.php

## Login Admin

```
Username: admin
Password: admin123
```

## Fitur

- ✅ Lihat berita MU dan Man City
- ✅ Tambah berita (Admin)
- ✅ Edit berita (Admin)
- ✅ Hapus berita (Admin)
- ✅ Upload gambar
- ✅ Filter berdasarkan tim dan kategori

## Struktur Database

- **articles**: Menyimpan berita
- **matches**: Menyimpan jadwal pertandingan

## Troubleshooting

**Database error?**
- Pastikan MySQL running di XAMPP
- Pastikan sudah import `schema.sql`

**Upload gambar gagal?**
- Pastikan folder `uploads/` ada dan bisa di-write
