<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class Kelas extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'kelas';

    protected $fillable = [
        'mata_kuliah_id',
        'dosen_id',
        'kode_kelas',
        'nama_kelas',
        'tahun_ajaran',
        'semester',
        'kapasitas',
        'terisi',
        'created_by',
        'created_at',
        'updated_by',
        'deleted_by'
    ];

    protected $dates = ['deleted_at', 'created_at'];

    /**
     * Relasi dengan Mata Kuliah
     */
    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class);
    }

    /**
     * Relasi dengan Dosen
     */
    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }

    /**
     * Relasi dengan Jadwal Kuliah
     */
    public function jadwalKuliah(): HasOne
    {
        return $this->hasOne(JadwalKuliah::class);
    }

    /**
     * Relasi dengan KRS
     */
    public function krs(): HasMany
    {
        return $this->hasMany(Krs::class);
    }

    /**
     * Alias relasi KRS (digunakan di beberapa controller/view).
     */
    public function krsItems(): HasMany
    {
        return $this->hasMany(Krs::class);
    }

    public function files(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }
}
