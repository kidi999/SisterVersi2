<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\AuditableTrait;

class AbsensiMahasiswa extends Model
{
    use SoftDeletes, AuditableTrait;

    protected $table = 'absensi_mahasiswa';

    protected $fillable = [
        'pertemuan_kuliah_id',
        'mahasiswa_id',
        'krs_id',
        'status_kehadiran',
        'waktu_absen',
        'is_terlambat',
        'menit_keterlambatan',
        'keterangan',
        'bukti_file',
        'latitude',
        'longitude',
        'metode_absensi',
        'is_verified',
        'verified_by',
        'verified_at',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'is_terlambat' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Relasi ke Pertemuan Kuliah
     */
    public function pertemuanKuliah(): BelongsTo
    {
        return $this->belongsTo(PertemuanKuliah::class, 'pertemuan_kuliah_id');
    }

    /**
     * Relasi ke Mahasiswa
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    /**
     * Relasi ke KRS
     */
    public function krs(): BelongsTo
    {
        return $this->belongsTo(Krs::class, 'krs_id');
    }

    /**
     * Relasi ke User - Verified By
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Relasi ke User - Created By
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke User - Updated By
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relasi ke User - Deleted By
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Hitung keterlambatan
     */
    public function hitungKeterlambatan($jamMulaiKuliah, $waktuAbsen)
    {
        $jamMulai = strtotime($jamMulaiKuliah);
        $jamAbsen = strtotime($waktuAbsen);
        
        if ($jamAbsen > $jamMulai) {
            $menitTerlambat = floor(($jamAbsen - $jamMulai) / 60);
            $this->is_terlambat = true;
            $this->menit_keterlambatan = $menitTerlambat;
        } else {
            $this->is_terlambat = false;
            $this->menit_keterlambatan = 0;
        }
        
        $this->save();
    }

    /**
     * Scope untuk status hadir
     */
    public function scopeHadir($query)
    {
        return $query->where('status_kehadiran', 'Hadir');
    }

    /**
     * Scope untuk status tidak hadir
     */
    public function scopeTidakHadir($query)
    {
        return $query->whereIn('status_kehadiran', ['Izin', 'Sakit', 'Alpa']);
    }

    /**
     * Scope untuk status alpa
     */
    public function scopeAlpa($query)
    {
        return $query->where('status_kehadiran', 'Alpa');
    }

    /**
     * Scope untuk yang terlambat
     */
    public function scopeTerlambat($query)
    {
        return $query->where('is_terlambat', true);
    }

    /**
     * Scope untuk yang sudah diverifikasi
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope untuk yang belum diverifikasi
     */
    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    /**
     * Hitung persentase kehadiran mahasiswa
     */
    public static function persentaseKehadiran($mahasiswaId, $kelasId = null)
    {
        $query = self::where('mahasiswa_id', $mahasiswaId);
        
        if ($kelasId) {
            $query->whereHas('pertemuanKuliah.jadwalKuliah', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }
        
        $totalPertemuan = $query->count();
        if ($totalPertemuan == 0) return 0;
        
        $hadir = $query->where('status_kehadiran', 'Hadir')->count();
        return round(($hadir / $totalPertemuan) * 100, 2);
    }
}
