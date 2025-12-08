<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\AuditableTrait;

class ProgramKerja extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'program_kerja';

    protected $fillable = [
        'rencana_kerja_tahunan_id',
        'kode_program',
        'nama_program',
        'deskripsi',
        'tujuan',
        'sasaran',
        'kategori',
        'tanggal_mulai',
        'tanggal_selesai',
        'anggaran',
        'realisasi_anggaran',
        'urutan',
        'penanggung_jawab_id',
        'status',
        'keterangan',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'anggaran' => 'decimal:2',
        'realisasi_anggaran' => 'decimal:2',
        'urutan' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function rencanaKerjaTahunan()
    {
        return $this->belongsTo(RencanaKerjaTahunan::class, 'rencana_kerja_tahunan_id');
    }

    public function penanggungJawab()
    {
        return $this->belongsTo(User::class, 'penanggung_jawab_id');
    }

    public function indikatorKinerja()
    {
        return $this->hasMany(IndikatorKinerja::class, 'program_kerja_id');
    }

    public function pencapaian()
    {
        return $this->hasMany(PencapaianRkt::class, 'program_kerja_id');
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        return $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Belum Dimulai' => 'secondary',
            'Berjalan' => 'primary',
            'Selesai' => 'success',
            'Tertunda' => 'warning',
            'Dibatalkan' => 'danger',
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    public function getPersentaseRealisasiAnggaranAttribute()
    {
        if ($this->anggaran == 0) return 0;
        return round(($this->realisasi_anggaran / $this->anggaran) * 100, 2);
    }

    public function getPersentasePencapaianIndikatorAttribute()
    {
        $totalIndikator = $this->indikatorKinerja()->count();
        if ($totalIndikator == 0) return 0;
        
        $totalBobot = $this->indikatorKinerja()->sum('bobot');
        if ($totalBobot == 0) return 0;
        
        $pencapaian = 0;
        foreach ($this->indikatorKinerja as $indikator) {
            if ($indikator->target > 0) {
                $persentase = ($indikator->realisasi / $indikator->target) * 100;
                $persentase = min($persentase, 100); // Max 100%
                $pencapaian += ($persentase * $indikator->bobot / 100);
            }
        }
        
        return round($pencapaian, 2);
    }

    // Scopes
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPenanggungJawab($query, $userId)
    {
        return $query->where('penanggung_jawab_id', $userId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan')->orderBy('tanggal_mulai');
    }
}
