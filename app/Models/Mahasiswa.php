<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class Mahasiswa extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'mahasiswa';

    protected $fillable = [
        'program_studi_id',
        'nim',
        'nama_mahasiswa',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'village_id',
        'telepon',
        'email',
        'tahun_masuk',
        'semester',
        'ipk',
        'status',
        'nama_wali',
        'telepon_wali',
        'created_by',
        'created_at',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'ipk' => 'decimal:2'
    ];

    protected $dates = ['deleted_at', 'created_at'];

    /**
     * Relasi dengan Village
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    /**
     * Relasi dengan Program Studi
     */
    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    /**
     * Relasi dengan KRS
     */
    public function krs(): HasMany
    {
        return $this->hasMany(Krs::class);
    }

    /**
     * Relasi dengan User (One-to-One)
     * Satu mahasiswa memiliki satu akun user
     * Foreign key 'mahasiswa_id' ada di tabel users
     */
    public function user()
    {
        return $this->hasOne(User::class, 'mahasiswa_id', 'id');
    }

    /**
     * Relasi ke tagihan mahasiswa
     */
    public function tagihanMahasiswa(): HasMany
    {
        return $this->hasMany(TagihanMahasiswa::class, 'mahasiswa_id');
    }

    /**
     * Relasi ke pembayaran mahasiswa
     */
    public function pembayaranMahasiswa(): HasMany
    {
        return $this->hasMany(PembayaranMahasiswa::class, 'mahasiswa_id');
    }

    /**
     * Relasi ke absensi mahasiswa
     */
    public function absensiMahasiswa(): HasMany
    {
        return $this->hasMany(AbsensiMahasiswa::class, 'mahasiswa_id');
    }
}
