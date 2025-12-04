<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditFields;

class KegiatanRkt extends Model
{
    use AuditFields;

    protected $table = 'kegiatan_rkt';

    protected $fillable = [
        'program_rkt_id',
        'kode_kegiatan',
        'nama_kegiatan',
        'deskripsi',
        'anggaran',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'urutan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'anggaran' => 'decimal:2',
    ];

    public function programRkt()
    {
        return $this->belongsTo(ProgramRkt::class);
    }

    public function pencapaianRkt()
    {
        return $this->hasMany(PencapaianRkt::class);
    }

    public function getPersentaseCapaianAttribute()
    {
        return $this->pencapaianRkt()->avg('persentase_capaian') ?? 0;
    }

    public function getTotalRealisasiAnggaranAttribute()
    {
        return $this->pencapaianRkt()->sum('realisasi_anggaran');
    }
}
