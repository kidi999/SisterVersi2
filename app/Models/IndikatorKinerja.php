<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\AuditableTrait;

class IndikatorKinerja extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'indikator_kinerja';

    protected $fillable = [
        'program_kerja_id',
        'nama_indikator',
        'deskripsi',
        'satuan',
        'target',
        'realisasi',
        'bobot',
        'urutan',
        'cara_pengukuran',
        'keterangan',
        'inserted_by',
        'inserted_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
    ];

    protected $casts = [
        'target' => 'decimal:2',
        'realisasi' => 'decimal:2',
        'bobot' => 'integer',
        'urutan' => 'integer',
        'inserted_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function programKerja()
    {
        return $this->belongsTo(ProgramKerja::class, 'program_kerja_id');
    }

    public function pencapaian()
    {
        return $this->hasMany(PencapaianRkt::class, 'indikator_kinerja_id');
    }

    // Accessors
    public function getPersentasePencapaianAttribute()
    {
        if ($this->target == 0) return 0;
        $persentase = ($this->realisasi / $this->target) * 100;
        return min(round($persentase, 2), 100); // Max 100%
    }

    public function getStatusPencapaianAttribute()
    {
        $persentase = $this->persentase_pencapaian;
        
        if ($persentase >= 100) return 'Tercapai';
        if ($persentase >= 75) return 'Hampir Tercapai';
        if ($persentase >= 50) return 'Sedang Berjalan';
        if ($persentase > 0) return 'Perlu Perhatian';
        return 'Belum Dimulai';
    }

    public function getStatusBadgeAttribute()
    {
        $persentase = $this->persentase_pencapaian;
        
        if ($persentase >= 100) return 'success';
        if ($persentase >= 75) return 'info';
        if ($persentase >= 50) return 'warning';
        if ($persentase > 0) return 'danger';
        return 'secondary';
    }

    // Scopes
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan');
    }

    public function scopeBySatuan($query, $satuan)
    {
        return $query->where('satuan', $satuan);
    }
}
