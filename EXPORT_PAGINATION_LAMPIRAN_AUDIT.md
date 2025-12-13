# Audit Export / Paginasi / Lampiran (13 Des 2025)

## Ringkasan
- Paket `maatwebsite/excel` **belum terpasang** (gagal install karena koneksi ke Packagist timeout di environment ini). Karena itu, implementasi **Export Excel** dibuat dalam format **`.xls` berbasis HTML table** (Excel-compatible) tanpa dependensi eksternal.

## Status modul Wilayah (sudah lengkap)
| Modul | Export Excel | Export PDF | Export CSV | Paginasi | Lampiran |
|---|---:|---:|---:|---:|---:|
| Provinsi | ✅ | ✅ | ✅ | ✅ | ✅ |
| Kabupaten/Kota | ✅ | ✅ | ✅ | ✅ | ✅ |
| Kecamatan | ✅ | ✅ | ✅ | ✅ | ✅ |
| Desa/Kelurahan | ✅ | ✅ | ✅ | ✅ | ✅ |

Catatan:
- Lampiran menggunakan `FileUpload` (polymorphic `morphMany`) dan endpoint upload ada di `FileUploadController`.

## Modul lain (belum semua punya export)
Hasil scan controller saat ini menunjukkan **hanya modul Wilayah** yang memiliki `exportExcel/exportPdf/exportCsv`.

Controller yang sudah memiliki paginasi (`paginate(...)`) dan banyak yang sudah punya lampiran, tetapi **belum ada export Excel/PDF**:
- Fakultas, Mahasiswa, Dosen, Program Studi, Mata Kuliah, Ruang
- Tahun Akademik, Semester
- Kelas, KRS, Nilai, Jadwal Kuliah
- Tagihan Mahasiswa, Pembayaran Mahasiswa
- Akreditasi Universitas/Fakultas/Program Studi
- Rencana Kerja Tahunan
- Users, Universities, dll.

## Langkah berikutnya (butuh konfirmasi scope)
Agar saya bisa “pastikan semua modul” benar-benar lengkap (Export Excel + PDF + Paginasi + Lampiran), saya perlu konfirmasi:
1) Modul mana saja yang wajib masuk scope (contoh: semua controller CRUD di menu admin, atau hanya Data Master tertentu)?
2) Export Excel/PDF: cukup **mengikuti kolom tabel di halaman index** (paling aman/minimal), atau ada format tertentu?
3) Lampiran: apakah cukup **fitur lampiran di create/edit + show** (yang sudah dipakai banyak modul), atau wajib ada lampiran juga di index?
