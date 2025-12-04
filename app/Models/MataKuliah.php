<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class MataKuliah extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'mata_kuliah';

    protected $fillable = [
        'level_matkul',
        'fakultas_id',
        'program_studi_id',
        'kode_mk',
        'nama_mk',
        'sks',
        'semester',
        'jenis',
        'deskripsi',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    /**
     * Relasi dengan Fakultas
     */
    public function fakultas(): BelongsTo
    {
        return $this->belongsTo(Fakultas::class);
    }

    /**
     * Relasi dengan Program Studi
     */
    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    /**
     * Relasi dengan Kelas
     */
    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }

    /**
     * Relasi polymorphic dengan FileUpload
     */
    public function files(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }

    /**
     * Relasi dengan User - Created By
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi dengan User - Updated By
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relasi dengan User - Deleted By
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Accessor untuk badge level
     */
    public function getLevelBadgeAttribute(): string
    {
        return match($this->level_matkul) {
            'universitas' => 'primary',
            'fakultas' => 'info',
            'prodi' => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Accessor untuk label level
     */
    public function getLevelLabelAttribute(): string
    {
        return match($this->level_matkul) {
            'universitas' => 'Universitas',
            'fakultas' => 'Fakultas',
            'prodi' => 'Program Studi',
            default => '-'
        };
    }

    /**
     * Accessor untuk scope label
     */
    public function getScopeLabelAttribute(): string
    {
        return match($this->level_matkul) {
            'universitas' => 'Semua Fakultas & Program Studi',
            'fakultas' => $this->fakultas ? 'Fakultas ' . $this->fakultas->nama_fakultas : '-',
            'prodi' => $this->programStudi ? $this->programStudi->nama_prodi : '-',
            default => '-'
        };
    }
}
