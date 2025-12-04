<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class Nilai extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'nilai';

    protected $fillable = [
        'krs_id',
        'nilai_tugas',
        'nilai_uts',
        'nilai_uas',
        'nilai_akhir',
        'nilai_huruf',
        'bobot',
        'inserted_by',
        'inserted_at',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'nilai_tugas' => 'decimal:2',
        'nilai_uts' => 'decimal:2',
        'nilai_uas' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
        'bobot' => 'decimal:2'
    ];

    protected $dates = ['deleted_at', 'inserted_at'];

    /**
     * Relasi dengan KRS
     */
    public function krs(): BelongsTo
    {
        return $this->belongsTo(Krs::class);
    }

    /**
     * Hitung nilai akhir otomatis
     */
    public function hitungNilaiAkhir(): void
    {
        $this->nilai_akhir = ($this->nilai_tugas * 0.3) + 
                             ($this->nilai_uts * 0.3) + 
                             ($this->nilai_uas * 0.4);
        $this->nilai_huruf = $this->konversiNilaiHuruf($this->nilai_akhir);
        $this->bobot = $this->konversiBobot($this->nilai_huruf);
    }

    /**
     * Konversi nilai angka ke huruf
     */
    private function konversiNilaiHuruf($nilai): string
    {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 80) return 'A-';
        if ($nilai >= 75) return 'B+';
        if ($nilai >= 70) return 'B';
        if ($nilai >= 65) return 'B-';
        if ($nilai >= 60) return 'C+';
        if ($nilai >= 55) return 'C';
        if ($nilai >= 50) return 'D';
        return 'E';
    }

    /**
     * Konversi nilai huruf ke bobot
     */
    private function konversiBobot($nilaiHuruf): float
    {
        return match($nilaiHuruf) {
            'A' => 4.00,
            'A-' => 3.75,
            'B+' => 3.50,
            'B' => 3.00,
            'B-' => 2.75,
            'C+' => 2.50,
            'C' => 2.00,
            'D' => 1.00,
            'E' => 0.00,
            default => 0.00
        };
    }
}
