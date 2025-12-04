# Perbaikan Relasi User-Mahasiswa dan Module KRS/Nilai

## 🐛 Error yang Terjadi

### Error 1: KRS Module
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'user_id' in 'where clause'
SQL: select * from `mahasiswa` where `user_id` = 7
```

### Error 2: Dashboard (sebelumnya)
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'nilai.mata_kuliah_id' in 'on clause'
```

## 🔍 Akar Masalah

### Kesalahpahaman Struktur Relasi

**SALAH KAPRAH** yang terjadi di berbagai controller:
```php
// ❌ SALAH - kolom user_id TIDAK ADA di tabel mahasiswa
$mahasiswa = Mahasiswa::where('user_id', $user->id)->first();

// ❌ SALAH - kolom user_id TIDAK ADA di tabel dosen
$dosen = Dosen::where('user_id', $user->id)->first();
```

### Struktur Database yang BENAR

#### Tabel: users
```sql
id
name
email
password
role_id              → roles(id)
fakultas_id          → fakultas(id)
program_studi_id     → program_studi(id)
dosen_id             → dosen(id)      ✅ Foreign key ada di users
mahasiswa_id         → mahasiswa(id)  ✅ Foreign key ada di users
is_active
```

#### Tabel: mahasiswa
```sql
id
program_studi_id
nim
nama_mahasiswa
email
...
```
**TIDAK ADA** kolom `user_id`!

#### Tabel: dosen
```sql
id
fakultas_id
nidn
nama_dosen
email
...
```
**TIDAK ADA** kolom `user_id`!

## ✅ Relasi yang Benar

### User → Mahasiswa (BelongsTo)
```php
// Model: User
public function mahasiswa(): BelongsTo
{
    return $this->belongsTo(Mahasiswa::class);
}

// Cara akses:
$user = Auth::user();
$mahasiswa = $user->mahasiswa;          // ✅ Benar
$mahasiswaId = $user->mahasiswa_id;     // ✅ Benar
```

### Mahasiswa → User (HasOne)
```php
// Model: Mahasiswa
public function user()
{
    return $this->hasOne(User::class, 'mahasiswa_id', 'id');
}

// Cara akses:
$mahasiswa = Mahasiswa::find(1);
$user = $mahasiswa->user;               // ✅ Benar
```

### Diagram Relasi
```
users
├─ mahasiswa_id ──► mahasiswa(id)
│                     └─ hasOne(User, 'mahasiswa_id')
├─ dosen_id ──► dosen(id)
│                 └─ hasOne(User, 'dosen_id')
└─ role_id ──► roles(id)
```

## 🛠️ Perbaikan yang Dilakukan

### 1. KrsController.php

#### Method: index()
**Sebelum (❌ SALAH)**:
```php
if ($user->role->name == 'mahasiswa') {
    $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
    if ($mahasiswa) {
        $query->where('mahasiswa_id', $mahasiswa->id);
    }
}
```

**Sesudah (✅ BENAR)**:
```php
if ($user->role->name == 'mahasiswa') {
    if ($user->mahasiswa_id) {
        $query->where('mahasiswa_id', $user->mahasiswa_id);
    } else {
        return redirect()->route('dashboard')
            ->with('error', 'Data mahasiswa tidak ditemukan. Silakan hubungi administrator.');
    }
}
```

#### Method: create()
**Sebelum (❌ SALAH)**:
```php
$mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
if (!$mahasiswa) {
    return redirect()->route('dashboard')
        ->with('error', 'Data mahasiswa tidak ditemukan');
}
```

**Sesudah (✅ BENAR)**:
```php
if (!$user->mahasiswa_id) {
    return redirect()->route('dashboard')
        ->with('error', 'Data mahasiswa tidak ditemukan');
}

$mahasiswa = $user->mahasiswa;
```

#### Method: store()
**Diperbaiki** sama seperti di atas.

#### Search Query
**Sebelum (❌ SALAH)**:
```php
$q->where('nim', 'like', "%{$search}%")
  ->orWhere('nama', 'like', "%{$search}%");  // kolom 'nama' tidak ada
```

**Sesudah (✅ BENAR)**:
```php
$q->where('nim', 'like', "%{$search}%")
  ->orWhere('nama_mahasiswa', 'like', "%{$search}%");  // kolom yang benar
```

### 2. NilaiController.php

#### Method: index()
**Diperbaiki** dengan pola yang sama:
```php
if ($user->role->name == 'mahasiswa') {
    if ($user->mahasiswa_id) {
        $query->whereHas('krs', function($q) use ($user) {
            $q->where('mahasiswa_id', $user->mahasiswa_id);
        });
    } else {
        return redirect()->route('dashboard')
            ->with('error', 'Data mahasiswa tidak ditemukan.');
    }
}
```

#### Search Query
**Diperbaiki** kolom nama menjadi `nama_mahasiswa`.

### 3. Mahasiswa.php Model

**Sebelum (❌ Kurang Lengkap)**:
```php
public function user()
{
    return $this->hasOne(User::class);  // Tidak jelas foreign key
}
```

**Sesudah (✅ BENAR & Jelas)**:
```php
/**
 * Relasi dengan User (One-to-One)
 * Satu mahasiswa memiliki satu akun user
 * Foreign key 'mahasiswa_id' ada di tabel users
 */
public function user()
{
    return $this->hasOne(User::class, 'mahasiswa_id', 'id');
}
```

### 4. DashboardController.php (Perbaikan Sebelumnya)

**Diperbaiki** query yang menggunakan join langsung menjadi menggunakan Eloquent relationships:
```php
// Total SKS Lulus
$nilaiLulus = Nilai::with(['krs.kelas.mataKuliah'])
    ->whereHas('krs', function($q) use ($user) {
        $q->where('mahasiswa_id', $user->mahasiswa_id);
    })
    ->whereIn('nilai_huruf', ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C'])
    ->get();

$totalSksLulus = $nilaiLulus->sum(function($nilai) {
    return $nilai->krs->kelas->mataKuliah->sks ?? 0;
});
```

## 📋 Pattern yang Benar untuk Akses Data

### 1. Get Mahasiswa dari User yang Login
```php
// ❌ SALAH
$mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();

// ✅ BENAR - Cara 1: Langsung dari relasi
$mahasiswa = Auth::user()->mahasiswa;

// ✅ BENAR - Cara 2: Cek foreign key dulu
$user = Auth::user();
if ($user->mahasiswa_id) {
    $mahasiswa = $user->mahasiswa;
}

// ✅ BENAR - Cara 3: Load dengan eager loading
$user = Auth::user()->load('mahasiswa');
$mahasiswa = $user->mahasiswa;
```

### 2. Get User dari Mahasiswa
```php
$mahasiswa = Mahasiswa::find(1);

// ✅ BENAR
$user = $mahasiswa->user;

// ✅ BENAR - dengan eager loading
$mahasiswa = Mahasiswa::with('user')->find(1);
$user = $mahasiswa->user;
```

### 3. Filter Query berdasarkan Role Mahasiswa
```php
$user = Auth::user();

// ❌ SALAH
$query->whereHas('mahasiswa', function($q) use ($user) {
    $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
    $q->where('id', $mahasiswa->id);
});

// ✅ BENAR
if ($user->mahasiswa_id) {
    $query->where('mahasiswa_id', $user->mahasiswa_id);
}
```

### 4. Search Query
```php
// ❌ SALAH - kolom 'nama' tidak ada
$query->whereHas('mahasiswa', function($q) use ($search) {
    $q->where('nama', 'like', "%{$search}%");
});

// ✅ BENAR - kolom yang benar adalah 'nama_mahasiswa'
$query->whereHas('mahasiswa', function($q) use ($search) {
    $q->where('nama_mahasiswa', 'like', "%{$search}%");
});
```

## 📁 File yang Dimodifikasi

1. ✏️ `app/Http/Controllers/KrsController.php`
   - Method: `index()` - Filter mahasiswa
   - Method: `create()` - Get mahasiswa
   - Method: `store()` - Get mahasiswa
   - Search query - Kolom nama_mahasiswa

2. ✏️ `app/Http/Controllers/NilaiController.php`
   - Method: `index()` - Filter mahasiswa
   - Search query - Kolom nama_mahasiswa

3. ✏️ `app/Models/Mahasiswa.php`
   - Relasi `user()` - Tambahkan foreign key explicit

4. ✏️ `app/Http/Controllers/DashboardController.php` (sebelumnya)
   - Total SKS lulus - Relasi yang benar
   - Hitung IPK - Relasi yang benar
   - KRS semester aktif - Filter yang benar

## ✅ Validasi Perbaikan

### Testing Checklist

- [x] Login sebagai mahasiswa
- [x] Akses `/krs` - TIDAK ada error "Column 'user_id' not found"
- [x] Akses `/nilai` - Data nilai muncul sesuai mahasiswa
- [x] Akses `/dashboard` - Dashboard mahasiswa tampil dengan benar
- [x] Filter/Search by NIM atau Nama - Menggunakan kolom yang benar
- [x] KRS create/store - Mahasiswa bisa mengisi KRS

### SQL Query yang Benar Sekarang

```sql
-- Dashboard: Total SKS Lulus
SELECT * FROM nilai
INNER JOIN krs ON nilai.krs_id = krs.id
INNER JOIN kelas ON krs.kelas_id = kelas.id
INNER JOIN mata_kuliah ON kelas.mata_kuliah_id = mata_kuliah.id
WHERE krs.mahasiswa_id = ?

-- KRS Index: Filter Mahasiswa
SELECT * FROM krs
WHERE mahasiswa_id = ?

-- Nilai Index: Filter Mahasiswa
SELECT * FROM nilai
WHERE EXISTS (
    SELECT * FROM krs 
    WHERE nilai.krs_id = krs.id 
    AND krs.mahasiswa_id = ?
)
```

## 🎯 Best Practices

1. **Pahami Struktur Database** sebelum membuat query
2. **Gunakan Relasi Eloquent** daripada query manual
3. **Selalu cek foreign key** ada di tabel mana
4. **Nama kolom yang konsisten**: `nama_mahasiswa`, `nama_dosen`, `nama_kelas`
5. **Eager Loading** untuk mencegah N+1 query
6. **Dokumentasi** relasi di model dengan komentar yang jelas
7. **Validasi** keberadaan data sebelum akses (null check)

## 🚀 Status Akhir

✅ **Module KRS** - Berfungsi normal untuk role mahasiswa  
✅ **Module Nilai** - Berfungsi normal untuk role mahasiswa  
✅ **Dashboard Mahasiswa** - Tampil data yang benar  
✅ **Relasi User-Mahasiswa** - Konsisten di seluruh sistem  
✅ **Search Query** - Menggunakan kolom yang benar  
✅ **No SQL Errors** - Semua query valid  

**Sistem sekarang sudah konsisten dan mengikuti desain database yang benar!** 🎉
