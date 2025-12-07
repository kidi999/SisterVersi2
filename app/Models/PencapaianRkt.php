<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\AuditableTrait;

class PencapaianRkt extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'pencapaian_rkt';

    protected $fillable = [
        'kegiatan_rkt_id',
        'periode',
        'tanggal_laporan',
        'capaian',
        'persentase_capaian',
        'realisasi_anggaran',
        'kendala',
        'solusi',
        'rencana_tindak_lanjut',
        'file_dokumentasi',
        'dilaporkan_oleh',
        'diverifikasi_oleh',
        'tanggal_verifikasi',
        'status_verifikasi',
        'catatan_verifikasi',
        'inserted_by',
        'inserted_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
    ];

    public $timestamps = false;

    protected $casts = [
        'tanggal_laporan' => 'date',
        'persentase_capaian' => 'decimal:2',
        'realisasi_anggaran' => 'decimal:2',
        'tanggal_verifikasi' => 'datetime',
        'inserted_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function kegiatanRkt()
    {
        return $this->belongsTo(KegiatanRkt::class, 'kegiatan_rkt_id');
    }

    public function dilaporkanOleh()
    {
        return $this->belongsTo(User::class, 'dilaporkan_oleh');
    }

    public function diverifikasiOleh()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        $statuses = [
            'Pending' => 'Menunggu Verifikasi',
            'Diverifikasi' => 'Diverifikasi',
            'Ditolak' => 'Ditolak',
        ];
        return $statuses[$this->status_verifikasi] ?? $this->status_verifikasi;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Pending' => 'warning',
            'Diverifikasi' => 'success',
            'Ditolak' => 'danger',
        ];
        return $badges[$this->status_verifikasi] ?? 'secondary';
    }

    // Scopes
    public function scopeByPeriode($query, $periode)
    {
        return $query->where('periode', $periode);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status_verifikasi', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status_verifikasi', 'Pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('status_verifikasi', 'Diverifikasi');
    }

    public function scopeRejected($query)
    {
        return $query->where('status_verifikasi', 'Ditolak');
    }

    public function scopeByPelapor($query, $userId)
    {
        return $query->where('dilaporkan_oleh', $userId);
    }

    // Methods
    public function verify($userId, $catatan = null)
    {
        $this->update([
            'status_verifikasi' => 'Diverifikasi',
            'diverifikasi_oleh' => $userId,
            'tanggal_verifikasi' => now(),
            'catatan_verifikasi' => $catatan,
        ]);

        // Update realisasi di indikator jika ada
        if ($this->indikator_kinerja_id && $this->indikatorKinerja) {
            $this->indikatorKinerja->update([
                'realisasi' => $this->nilai_pencapaian
            ]);
        }

        // Update realisasi anggaran di program kerja jika diperlukan
        // Bisa disesuaikan dengan logika bisnis
    }

    public function reject($userId, $catatan)
    {
        $this->update([
            'status_verifikasi' => 'Ditolak',
            'diverifikasi_oleh' => $userId,
            'tanggal_verifikasi' => now(),
            'catatan_verifikasi' => $catatan,
        ]);
    }
}
