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
        'program_kerja_id',
        'indikator_kinerja_id',
        'tanggal_laporan',
        'periode',
        'deskripsi_pencapaian',
        'nilai_pencapaian',
        'persentase_pencapaian',
        'kendala',
        'solusi',
        'tindak_lanjut',
        'dokumen_pendukung',
        'dilaporkan_oleh',
        'status_verifikasi',
        'diverifikasi_oleh',
        'tanggal_verifikasi',
        'catatan_verifikasi',
        'inserted_by',
        'inserted_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
    ];

    protected $casts = [
        'tanggal_laporan' => 'date',
        'nilai_pencapaian' => 'decimal:2',
        'persentase_pencapaian' => 'decimal:2',
        'tanggal_verifikasi' => 'datetime',
        'inserted_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function programKerja()
    {
        return $this->belongsTo(ProgramKerja::class, 'program_kerja_id');
    }

    public function indikatorKinerja()
    {
        return $this->belongsTo(IndikatorKinerja::class, 'indikator_kinerja_id');
    }

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'dilaporkan_oleh');
    }

    public function verifikator()
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
