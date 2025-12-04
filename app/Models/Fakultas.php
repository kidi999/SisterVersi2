<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class Fakultas extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'fakultas';

    protected $fillable = [
        'kode_fakultas',
        'nama_fakultas',
        'singkatan',
        'dekan_id',
        'dekan',
        'alamat',
        'telepon',
        'email',
        'village_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

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
    public function programStudi(): HasMany
    {
        return $this->hasMany(ProgramStudi::class);
    }

    /**
     * Relasi dengan Dosen (Dekan aktif)
     */
    public function dekanAktif(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dekan_id');
    }

    /**
     * Relasi dengan Jabatan Struktural (Semua dekan)
     */
    public function riwayatDekan(): HasMany
    {
        return $this->hasMany(JabatanStruktural::class)->where('jenis_jabatan', 'dekan')->orderBy('tanggal_mulai', 'desc');
    }

    /**
     * Get dekan aktif dari jabatan struktural
     */
    public function getDekanAktifFromJabatanAttribute()
    {
        return $this->riwayatDekan()->where('status', 'aktif')->first();
    }

    /**
     * Relasi dengan Dosen
     */
    public function dosen(): HasMany
    {
        return $this->hasMany(Dosen::class);
    }

    /**
     * Relasi dengan Users (One-to-Many)
     * Users yang terdaftar di fakultas ini (admin_fakultas, dll)
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relasi polymorphic dengan FileUpload
     */
    public function files()
    {
        return $this->morphMany(FileUpload::class, 'fileable')->orderBy('order');
    }
}
