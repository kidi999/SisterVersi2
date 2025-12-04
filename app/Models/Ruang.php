<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class Ruang extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'ruang';

    protected $fillable = [
        'kode_ruang',
        'nama_ruang',
        'gedung',
        'lantai',
        'kapasitas',
        'jenis_ruang',
        'tingkat_kepemilikan',
        'fakultas_id',
        'program_studi_id',
        'fasilitas',
        'status',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

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
     * Relasi dengan File Upload (Polymorphic)
     */
    public function files(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }

    /**
     * Accessor untuk kepemilikan display
     */
    public function getKepemilikanDisplayAttribute()
    {
        switch ($this->tingkat_kepemilikan) {
            case 'Universitas':
                return '<span class="badge bg-primary">Universitas</span>';
            case 'Fakultas':
                return '<span class="badge bg-info">' . ($this->fakultas->nama_fakultas ?? 'Fakultas') . '</span>';
            case 'Prodi':
                return '<span class="badge bg-success">' . ($this->programStudi->nama_prodi ?? 'Prodi') . '</span>';
            default:
                return '<span class="badge bg-secondary">-</span>';
        }
    }

    /**
     * Accessor untuk status badge
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Aktif' => 'success',
            'Tidak Aktif' => 'secondary',
            'Dalam Perbaikan' => 'warning'
        ];

        $class = $badges[$this->status] ?? 'secondary';
        return $class;
    }

    /**
     * Cek apakah ruang bisa digunakan oleh prodi tertentu
     */
    public function canBeUsedBy($prodiId, $fakultasId = null)
    {
        // Ruang universitas bisa digunakan semua
        if ($this->tingkat_kepemilikan === 'Universitas') {
            return true;
        }

        // Ruang fakultas bisa digunakan semua prodi di fakultas tersebut
        if ($this->tingkat_kepemilikan === 'Fakultas') {
            return $this->fakultas_id == $fakultasId;
        }

        // Ruang prodi hanya bisa digunakan prodi tersebut
        if ($this->tingkat_kepemilikan === 'Prodi') {
            return $this->program_studi_id == $prodiId;
        }

        return false;
    }

    /**
     * Scope untuk filter berdasarkan akses prodi
     */
    public function scopeAccessibleByProdi($query, $prodiId, $fakultasId = null)
    {
        return $query->where(function($q) use ($prodiId, $fakultasId) {
            $q->where('tingkat_kepemilikan', 'Universitas')
              ->orWhere(function($q2) use ($fakultasId) {
                  $q2->where('tingkat_kepemilikan', 'Fakultas')
                     ->where('fakultas_id', $fakultasId);
              })
              ->orWhere(function($q3) use ($prodiId) {
                  $q3->where('tingkat_kepemilikan', 'Prodi')
                     ->where('program_studi_id', $prodiId);
              });
        });
    }
}
