<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class AkreditasiFakultas extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'akreditasi_fakultas';

    protected $fillable = [
        'fakultas_id',
        'lembaga_akreditasi',
        'nomor_sk',
        'tanggal_sk',
        'tanggal_berakhir',
        'peringkat',
        'tahun_akreditasi',
        'catatan',
        'status',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
        'tanggal_berakhir' => 'date',
        'tahun_akreditasi' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relasi dengan Fakultas
     */
    public function fakultas(): BelongsTo
    {
        return $this->belongsTo(Fakultas::class);
    }

    /**
     * Relasi polymorphic dengan FileUpload
     */
    public function files(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }

    /**
     * Scope untuk filter status
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'Aktif' => 'success',
            'Kadaluarsa' => 'danger',
            'Dalam Proses' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Get peringkat badge class
     */
    public function getPeringkatBadgeAttribute()
    {
        return match($this->peringkat) {
            'Unggul', 'A' => 'success',
            'Baik Sekali', 'B' => 'primary',
            'Baik', 'C' => 'info',
            'Belum Terakreditasi' => 'secondary',
            default => 'secondary'
        };
    }
}
