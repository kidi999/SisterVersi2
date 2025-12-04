<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class Krs extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'krs';

    protected $fillable = [
        'mahasiswa_id',
        'kelas_id',
        'tahun_ajaran',
        'semester',
        'status',
        'tanggal_pengajuan',
        'tanggal_persetujuan',
        'inserted_by',
        'inserted_at',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'datetime',
        'tanggal_persetujuan' => 'datetime'
    ];

    protected $dates = ['deleted_at', 'inserted_at'];

    /**
     * Relasi dengan Mahasiswa
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    /**
     * Relasi dengan Kelas
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Relasi dengan Nilai
     */
    public function nilai(): HasOne
    {
        return $this->hasOne(Nilai::class);
    }

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

    /**
     * Relasi ke absensi mahasiswa
     */
    public function absensiMahasiswa(): HasMany
    {
        return $this->hasMany(AbsensiMahasiswa::class, 'krs_id');
    }
}
