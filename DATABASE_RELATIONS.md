# Struktur Relasi Database SISTER

## Diagram Relasi Utama

### 1. Relasi Akademik Mahasiswa
```
User
  ├─ mahasiswa_id ──► Mahasiswa
                        ├─ program_studi_id ──► ProgramStudi
                        │                         └─ fakultas_id ──► Fakultas
                        └─ (KRS, Nilai melalui KRS)
```

### 2. Relasi KRS dan Nilai
```
Mahasiswa
  └─ KRS (many)
       ├─ mahasiswa_id ──► Mahasiswa
       ├─ kelas_id ──► Kelas
       │                 ├─ mata_kuliah_id ──► MataKuliah (SKS, Kode, Nama)
       │                 └─ dosen_id ──► Dosen
       └─ Nilai (one)
            ├─ krs_id ──► KRS
            └─ nilai_huruf, nilai_akhir, bobot
```

**PENTING**: Nilai TIDAK memiliki kolom `mata_kuliah_id` secara langsung!
- Nilai → KRS → Kelas → MataKuliah

### 3. Path untuk Mendapatkan Mata Kuliah dari Nilai
```php
// ❌ SALAH - mata_kuliah_id tidak ada di tabel nilai
$nilai->mata_kuliah_id

// ✅ BENAR - melalui relasi KRS → Kelas → MataKuliah
$nilai->krs->kelas->mataKuliah->sks
$nilai->krs->kelas->mataKuliah->nama_mata_kuliah
```

### 4. Path untuk Mendapatkan Mata Kuliah dari KRS
```php
// ❌ SALAH - KRS tidak punya relasi langsung ke MataKuliah
$krs->mataKuliah->sks

// ✅ BENAR - melalui relasi Kelas
$krs->kelas->mataKuliah->sks
$krs->kelas->mataKuliah->nama_mata_kuliah

// ✅ ALTERNATIF - menggunakan HasOneThrough (sudah ditambahkan)
$krs->mataKuliah->sks // sekarang bisa karena ada relasi HasOneThrough
```

## Struktur Tabel

### Tabel: nilai
```sql
id                  BIGINT PRIMARY KEY
krs_id              BIGINT FOREIGN KEY → krs(id)
nilai_tugas         DECIMAL(5,2)
nilai_uts           DECIMAL(5,2)
nilai_uas           DECIMAL(5,2)
nilai_akhir         DECIMAL(5,2)
nilai_huruf         CHAR(2)
bobot               DECIMAL(3,2)
timestamps
deleted_at
```

**TIDAK ADA**: `mata_kuliah_id`, `mahasiswa_id`

### Tabel: krs
```sql
id                  BIGINT PRIMARY KEY
mahasiswa_id        BIGINT FOREIGN KEY → mahasiswa(id)
kelas_id            BIGINT FOREIGN KEY → kelas(id)
tahun_ajaran        VARCHAR(10) -- Format: "2024/2025"
semester            ENUM('Ganjil', 'Genap')
status              ENUM('Draft', 'Diajukan', 'Disetujui', 'Ditolak')
tanggal_pengajuan   TIMESTAMP
tanggal_persetujuan TIMESTAMP
timestamps
deleted_at
```

**TIDAK ADA**: `semester_id`, `mata_kuliah_id`

### Tabel: kelas
```sql
id                  BIGINT PRIMARY KEY
mata_kuliah_id      BIGINT FOREIGN KEY → mata_kuliah(id)
dosen_id            BIGINT FOREIGN KEY → dosen(id)
kode_kelas          VARCHAR
nama_kelas          VARCHAR
tahun_ajaran        VARCHAR(10)
semester            ENUM('Ganjil', 'Genap')
kapasitas           INT
terisi              INT
timestamps
deleted_at
```

### Tabel: mata_kuliah
```sql
id                  BIGINT PRIMARY KEY
program_studi_id    BIGINT FOREIGN KEY
kode_mata_kuliah    VARCHAR
nama_mata_kuliah    VARCHAR
sks                 INT
semester            INT
timestamps
deleted_at
```

## Query Pattern yang Benar

### 1. Total SKS Lulus (dari Nilai)
```php
// ❌ SALAH - join langsung ke mata_kuliah
Nilai::join('mata_kuliah', 'nilai.mata_kuliah_id', '=', 'mata_kuliah.id')
    ->sum('mata_kuliah.sks');

// ✅ BENAR - melalui relasi
$nilaiLulus = Nilai::with(['krs.kelas.mataKuliah'])
    ->whereHas('krs', function($q) use ($user) {
        $q->where('mahasiswa_id', $user->mahasiswa_id);
    })
    ->whereIn('nilai_huruf', ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C'])
    ->get();

$totalSks = $nilaiLulus->sum(function($nilai) {
    return $nilai->krs->kelas->mataKuliah->sks ?? 0;
});
```

### 2. KRS Semester Aktif
```php
// ❌ SALAH - semester_id tidak ada
Krs::where('semester_id', $semesterAktif->id)->get();

// ✅ BENAR - filter by tahun_ajaran dan semester
$krsAktif = Krs::with(['kelas.mataKuliah', 'kelas.dosen'])
    ->where('mahasiswa_id', $user->mahasiswa_id)
    ->where('tahun_ajaran', '2024/2025')
    ->where('semester', 'Ganjil')
    ->where('status', 'Disetujui')
    ->get();
```

### 3. Hitung IPK
```php
$nilaiData = Nilai::with(['krs.kelas.mataKuliah'])
    ->whereHas('krs', function($q) use ($user) {
        $q->where('mahasiswa_id', $user->mahasiswa_id);
    })
    ->get();

$totalBobotXSks = 0;
$totalSks = 0;

foreach ($nilaiData as $nilai) {
    $sks = $nilai->krs->kelas->mataKuliah->sks ?? 0;
    $bobot = $gradePoints[$nilai->nilai_huruf] ?? 0;
    $totalBobotXSks += ($bobot * $sks);
    $totalSks += $sks;
}

$ipk = $totalSks > 0 ? round($totalBobotXSks / $totalSks, 2) : 0;
```

## Eager Loading yang Benar

### Dashboard Mahasiswa
```php
// Load mahasiswa dengan relasi lengkap
$mahasiswa = Mahasiswa::with(['programStudi.fakultas'])
    ->where('id', $user->mahasiswa_id)
    ->first();

// Load nilai dengan relasi ke mata kuliah
$nilaiData = Nilai::with(['krs.kelas.mataKuliah'])
    ->whereHas('krs', function($q) use ($user) {
        $q->where('mahasiswa_id', $user->mahasiswa_id);
    })
    ->get();

// Load KRS semester aktif dengan semua relasi
$krsAktif = Krs::with(['kelas.mataKuliah', 'kelas.dosen'])
    ->where('mahasiswa_id', $user->mahasiswa_id)
    ->where('tahun_ajaran', $tahunAjaran)
    ->where('semester', $semesterType)
    ->get();
```

## Model Relations yang Ditambahkan

### Krs Model - HasOneThrough
```php
/**
 * Relasi dengan Mata Kuliah melalui Kelas
 * KRS -> Kelas -> MataKuliah
 */
public function mataKuliah(): HasOneThrough
{
    return $this->hasOneThrough(
        MataKuliah::class,  // Model tujuan
        Kelas::class,       // Model perantara
        'id',               // Foreign key di kelas (ke krs)
        'id',               // Foreign key di mata_kuliah (ke kelas)
        'kelas_id',         // Local key di krs
        'mata_kuliah_id'    // Local key di kelas
    );
}
```

Dengan relasi ini, sekarang bisa menggunakan:
```php
// Cara lama (masih valid)
$krs->kelas->mataKuliah->sks

// Cara baru (dengan HasOneThrough)
$krs->mataKuliah->sks
```

## Best Practices

1. **Selalu gunakan eager loading** untuk mencegah N+1 query problem
2. **Pahami struktur relasi** sebelum membuat query
3. **Gunakan `with()`** untuk load relasi nested: `with(['krs.kelas.mataKuliah'])`
4. **Gunakan `whereHas()`** untuk filter berdasarkan relasi
5. **Hindari join manual** jika sudah ada relasi Eloquent
6. **Test query dengan tinker** sebelum implementasi di controller

## Testing dengan Tinker

```bash
php artisan tinker

# Test relasi Nilai → KRS → Kelas → MataKuliah
$nilai = Nilai::with(['krs.kelas.mataKuliah'])->first();
$nilai->krs->kelas->mataKuliah->nama_mata_kuliah;

# Test relasi KRS → Kelas → MataKuliah
$krs = Krs::with(['kelas.mataKuliah'])->first();
$krs->kelas->mataKuliah->sks;

# Test HasOneThrough (jika sudah ditambahkan)
$krs = Krs::with('mataKuliah')->first();
$krs->mataKuliah->sks;
```

## Common Errors dan Solusi

### Error: Column not found 'nilai.mata_kuliah_id'
**Penyebab**: Query join langsung tanpa melalui relasi
```php
// ❌ SALAH
Nilai::join('mata_kuliah', 'nilai.mata_kuliah_id', '=', 'mata_kuliah.id')
```
**Solusi**: Gunakan eager loading dan akses melalui relasi
```php
// ✅ BENAR
$nilai->krs->kelas->mataKuliah
```

### Error: Column not found 'krs.semester_id'
**Penyebab**: Tabel KRS tidak punya semester_id, pakai tahun_ajaran + semester
```php
// ❌ SALAH
Krs::where('semester_id', $id)
```
**Solusi**: Filter dengan tahun_ajaran dan semester
```php
// ✅ BENAR
Krs::where('tahun_ajaran', '2024/2025')->where('semester', 'Ganjil')
```

## Kesimpulan

Sistem ini menggunakan pola relasi **nested relationships** yang dalam:
- User → Mahasiswa → KRS → Kelas → MataKuliah
- User → Mahasiswa → KRS → Nilai (tanpa mata_kuliah_id langsung)

Selalu ikuti path relasi yang benar dan gunakan eager loading untuk performa optimal.
