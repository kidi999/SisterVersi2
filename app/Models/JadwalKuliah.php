<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;
use Illuminate\Support\Facades\DB;

class JadwalKuliah extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'jadwal_kuliah';

    protected $fillable = [
        'kelas_id',
        'tahun_akademik_id',
        'semester_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruang_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relasi dengan Kelas
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Relasi dengan Tahun Akademik
     */
    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    /**
     * Relasi dengan Semester
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Relasi dengan Ruang
     */
    public function ruang(): BelongsTo
    {
        return $this->belongsTo(Ruang::class);
    }

    /**
     * Relasi dengan User - Created By
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi dengan User - Updated By
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relasi dengan User - Deleted By
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Check if ruangan bentrok dengan jadwal lain
     */
    public static function checkRuangConflict($ruangId, $hari, $jamMulai, $jamSelesai, $excludeId = null)
    {
        $query = self::where('ruang_id', $ruangId)
            ->where('hari', $hari)
            ->where(function($q) use ($jamMulai, $jamSelesai) {
                // Check if time overlaps
                $q->whereBetween('jam_mulai', [$jamMulai, $jamSelesai])
                  ->orWhereBetween('jam_selesai', [$jamMulai, $jamSelesai])
                  ->orWhere(function($q2) use ($jamMulai, $jamSelesai) {
                      $q2->where('jam_mulai', '<=', $jamMulai)
                         ->where('jam_selesai', '>=', $jamSelesai);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->with(['kelas.mataKuliah', 'kelas.dosen'])->first();
    }

    /**
     * Check if dosen bentrok dengan jadwal lain
     */
    public static function checkDosenConflict($kelasId, $hari, $jamMulai, $jamSelesai, $excludeId = null)
    {
        $kelas = Kelas::find($kelasId);
        if (!$kelas) return null;

        $query = self::whereHas('kelas', function($q) use ($kelas) {
                $q->where('dosen_id', $kelas->dosen_id);
            })
            ->where('hari', $hari)
            ->where(function($q) use ($jamMulai, $jamSelesai) {
                $q->whereBetween('jam_mulai', [$jamMulai, $jamSelesai])
                  ->orWhereBetween('jam_selesai', [$jamMulai, $jamSelesai])
                  ->orWhere(function($q2) use ($jamMulai, $jamSelesai) {
                      $q2->where('jam_mulai', '<=', $jamMulai)
                         ->where('jam_selesai', '>=', $jamSelesai);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->with(['kelas.mataKuliah'])->first();
    }

    /**
     * Get formatted time display
     */
    public function getWaktuAttribute()
    {
        return $this->jam_mulai . ' - ' . $this->jam_selesai;
    }

    /**
     * Get badge class for hari
     */
    public function getHariBadgeAttribute()
    {
        $badges = [
            'Senin' => 'primary',
            'Selasa' => 'success',
            'Rabu' => 'info',
            'Kamis' => 'warning',
            'Jumat' => 'danger',
            'Sabtu' => 'secondary'
        ];

        return $badges[$this->hari] ?? 'secondary';
    }

    /**
     * Relasi ke Pertemuan Kuliah
     */
    public function pertemuanKuliah(): HasMany
    {
        return $this->hasMany(PertemuanKuliah::class, 'jadwal_kuliah_id');
    }
}
