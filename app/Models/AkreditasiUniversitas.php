<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class AkreditasiUniversitas extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'akreditasi_universitas';

    protected $fillable = [
        'university_id',
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
     * Relasi dengan University
     */
    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
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
