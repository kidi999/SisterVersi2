<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class Semester extends Model
{
    use SoftDeletes, AuditableTrait;

    protected $fillable = [
        'tahun_akademik_id',
        'program_studi_id',
        'nama_semester',
        'nomor_semester',
        'tanggal_mulai',
        'tanggal_selesai',
        'tanggal_mulai_perkuliahan',
        'tanggal_selesai_perkuliahan',
        'tanggal_mulai_uts',
        'tanggal_selesai_uts',
        'tanggal_mulai_uas',
        'tanggal_selesai_uas',
        'is_active',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_mulai_perkuliahan' => 'date',
        'tanggal_selesai_perkuliahan' => 'date',
        'tanggal_mulai_uts' => 'date',
        'tanggal_selesai_uts' => 'date',
        'tanggal_mulai_uas' => 'date',
        'tanggal_selesai_uas' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    const SEMESTER_GANJIL = 'Ganjil';
    const SEMESTER_GENAP = 'Genap';
    const SEMESTER_PENDEK = 'Pendek';

    /**
     * Relasi dengan Tahun Akademik
     */
    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    /**
     * Relasi dengan Program Studi
     */
    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    /**
     * Relasi dengan File Upload (polymorphic)
     */
    public function files(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }

    /**
     * Scope untuk semester aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk semester universitas (tidak spesifik prodi)
     */
    public function scopeUniversitas($query)
    {
        return $query->whereNull('program_studi_id');
    }

    /**
     * Scope untuk semester prodi
     */
    public function scopeProdi($query, $prodiId)
    {
        return $query->where('program_studi_id', $prodiId);
    }

    /**
     * Get fakultas melalui program studi
     */
    public function getFakultasAttribute()
    {
        return $this->programStudi?->fakultas;
    }
}
