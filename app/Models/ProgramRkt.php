<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\AuditableTrait;

class ProgramRkt extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'program_rkt';

    public $timestamps = false;

    protected $fillable = [
        'rencana_kerja_tahunan_id',
        'kode_program',
        'nama_program',
        'deskripsi',
        'kategori',
        'anggaran',
        'target_mulai',
        'target_selesai',
        'penanggung_jawab',
        'indikator_kinerja',
        'urutan',
    ];

    protected $casts = [
        'target_mulai' => 'date',
        'target_selesai' => 'date',
        'anggaran' => 'decimal:2',
    ];

    public function rencanaKerjaTahunan()
    {
        return $this->belongsTo(RencanaKerjaTahunan::class);
    }

    public function kegiatanRkt()
    {
        return $this->hasMany(KegiatanRkt::class);
    }

    public function getTotalRealisasiAnggaranAttribute()
    {
        return $this->kegiatanRkt()->sum('anggaran');
    }

    public function getPersentaseSelesaiAttribute()
    {
        $total = $this->kegiatanRkt()->count();
        if ($total === 0) return 0;
        
        $selesai = $this->kegiatanRkt()->where('status', 'Selesai')->count();
        return round(($selesai / $total) * 100, 2);
    }
}
