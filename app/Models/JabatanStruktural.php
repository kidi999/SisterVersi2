<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\AuditableTrait;

class JabatanStruktural extends Model
{
    use SoftDeletes, AuditableTrait;

    protected $table = 'jabatan_struktural';

    protected $fillable = [
        'dosen_id',
        'fakultas_id',
        'program_studi_id',
        'jenis_jabatan',
        'nama_jabatan',
        'nomor_sk',
        'tanggal_sk',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'keterangan',
        'file_sk_path',
        'created_by'
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relasi ke Dosen
     */
    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }

    /**
     * Relasi ke Fakultas
     */
    public function fakultas(): BelongsTo
    {
        return $this->belongsTo(Fakultas::class);
    }

    /**
     * Relasi ke Program Studi
     */
    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    /**
     * Scope untuk jabatan aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope untuk jabatan dekan
     */
    public function scopeDekan($query)
    {
        return $query->where('jenis_jabatan', 'dekan');
    }

    /**
     * Scope untuk jabatan pada fakultas tertentu
     */
    public function scopeFakultas($query, $fakultasId)
    {
        return $query->where('fakultas_id', $fakultasId);
    }

    /**
     * Get durasi jabatan in years and months
     */
    public function getDurasiAttribute()
    {
        $start = $this->tanggal_mulai;
        $end = $this->tanggal_selesai ?? now();
        
        $diff = $start->diff($end);
        
        if ($diff->y > 0) {
            return $diff->y . ' tahun ' . $diff->m . ' bulan';
        }
        
        return $diff->m . ' bulan';
    }

    /**
     * Check if jabatan is currently active
     */
    public function getIsAktifAttribute()
    {
        return $this->status === 'aktif' && 
               $this->tanggal_mulai <= now() && 
               ($this->tanggal_selesai === null || $this->tanggal_selesai >= now());
    }

    /**
     * Get badge color based on status
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'aktif' => 'success',
            'selesai' => 'secondary',
            'diberhentikan' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get jenis jabatan label
     */
    public function getJenisJabatanLabelAttribute()
    {
        return match($this->jenis_jabatan) {
            'rektor' => 'Rektor',
            'wakil_rektor' => 'Wakil Rektor',
            'dekan' => 'Dekan',
            'wakil_dekan' => 'Wakil Dekan',
            'ketua_prodi' => 'Ketua Program Studi',
            'sekretaris_prodi' => 'Sekretaris Program Studi',
            'direktur' => 'Direktur',
            'kepala_pusat' => 'Kepala Pusat',
            'kepala_biro' => 'Kepala Biro',
            'kepala_bagian' => 'Kepala Bagian',
            'kepala_lab' => 'Kepala Laboratorium',
            'lainnya' => 'Lainnya',
            default => $this->jenis_jabatan
        };
    }

    /**
     * Polymorphic relationship untuk file SK
     */
    public function files()
    {
        return $this->morphMany(FileUpload::class, 'fileable')->orderBy('order');
    }
}
