<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\AuditableTrait;

class RencanaKerjaTahunan extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'rencana_kerja_tahunan';

    public $timestamps = false;

    // Status constants
    const STATUS_DRAFT = 'Draft';
    const STATUS_DIAJUKAN = 'Diajukan';
    const STATUS_DISETUJUI = 'Disetujui';
    const STATUS_DITOLAK = 'Ditolak';
    const STATUS_DALAM_PROSES = 'Dalam Proses';
    const STATUS_SELESAI = 'Selesai';
    const STATUS_DIBATALKAN = 'Dibatalkan';

    protected $fillable = [
        'kode_rkt',
        'judul_rkt',
        'deskripsi',
        'tahun',
        'level',
        'university_id',
        'fakultas_id',
        'program_studi_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'anggaran',
        'status',
        'catatan_penolakan',
        'disetujui_oleh',
        'tanggal_disetujui',
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
        'tanggal_disetujui' => 'date',
        'anggaran' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function university()
    {
        return $this->belongsTo(University::class, 'university_id');
    }

    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_id');
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'program_studi_id');
    }

    public function programRkt()
    {
        return $this->hasMany(ProgramRkt::class, 'rencana_kerja_tahunan_id');
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    // Accessors
    public function getLevelTextAttribute()
    {
        return $this->level;
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'Draft' => 'Draft',
            'Diajukan' => 'Diajukan',
            'Disetujui' => 'Disetujui',
            'Ditolak' => 'Ditolak',
            'Dalam Proses' => 'Dalam Proses',
            'Selesai' => 'Selesai',
            'Dibatalkan' => 'Dibatalkan',
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'Draft' => 'secondary',
            'Diajukan' => 'warning',
            'Disetujui' => 'success',
            'Ditolak' => 'danger',
            'Dalam Proses' => 'primary',
            'Selesai' => 'info',
            'Dibatalkan' => 'dark',
        ];
        return $badges[$this->status] ?? 'secondary';
    }

    public function getTotalAnggaranProgramAttribute()
    {
        return $this->programRkt()->sum('anggaran');
    }

    public function getTotalRealisasiAnggaranAttribute()
    {
        return $this->programRkt->sum(function($program) {
            return $program->totalRealisasiAnggaran;
        });
    }

    public function getPersentasePencapaianAttribute()
    {
        $totalProgram = $this->programRkt()->count();
        if ($totalProgram == 0) return 0;
        
        $totalPersentase = $this->programRkt->sum(function($program) {
            return $program->persentaseSelesai;
        });
        return round($totalPersentase / $totalProgram, 2);
    }

    // Scopes
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeByTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeUniversitas($query)
    {
        return $query->where('level', 'Universitas');
    }

    public function scopeFakultas($query, $fakultasId = null)
    {
        $query = $query->where('level', 'Fakultas');
        if ($fakultasId) {
            $query->where('fakultas_id', $fakultasId);
        }
        return $query;
    }

    public function scopeProdi($query, $prodiId = null)
    {
        $query = $query->where('level', 'Prodi');
        if ($prodiId) {
            $query->where('program_studi_id', $prodiId);
        }
        return $query;
    }

    // Methods
    public function generateKodeRkt()
    {
        $prefix = [
            'Universitas' => 'RKTU',
            'Fakultas' => 'RKTF',
            'Prodi' => 'RKTP',
        ];
        
        $prefixCode = $prefix[$this->level] ?? 'RKT';
        $lastRkt = self::where('kode_rkt', 'like', "$prefixCode/$this->tahun/%")
            ->orderBy('kode_rkt', 'desc')
            ->first();
        
        if ($lastRkt) {
            $lastNumber = intval(substr($lastRkt->kode_rkt, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "$prefixCode/$this->tahun/$newNumber";
    }

    public function canEdit()
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_DITOLAK]);
    }

    public function canDelete()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canSubmit()
    {
        return $this->status === self::STATUS_DRAFT && $this->programRkt()->count() > 0;
    }

    public function canApprove()
    {
        return $this->status === self::STATUS_DIAJUKAN;
    }

    public function getTotalAnggaran()
    {
        return $this->totalAnggaranProgram;
    }

    public function getPersentasePencapaian()
    {
        return $this->persentasePencapaian;
    }
}
