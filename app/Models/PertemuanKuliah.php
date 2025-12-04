<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\AuditableTrait;

class PertemuanKuliah extends Model
{
    use SoftDeletes, AuditableTrait;

    protected $table = 'pertemuan_kuliah';

    protected $fillable = [
        'jadwal_kuliah_id',
        'tahun_akademik_id',
        'semester_id',
        'pertemuan_ke',
        'tanggal_pertemuan',
        'jam_mulai_actual',
        'jam_selesai_actual',
        'topik_bahasan',
        'materi',
        'catatan',
        'status',
        'alasan_batal',
        'tanggal_pengganti',
        'file_materi',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'tanggal_pertemuan' => 'date',
        'tanggal_pengganti' => 'date',
    ];

    /**
     * Relasi ke Jadwal Kuliah
     */
    public function jadwalKuliah(): BelongsTo
    {
        return $this->belongsTo(JadwalKuliah::class, 'jadwal_kuliah_id');
    }

    /**
     * Relasi ke Tahun Akademik
     */
    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    /**
     * Relasi ke Semester
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    /**
     * Relasi ke Absensi Mahasiswa
     */
    public function absensiMahasiswa(): HasMany
    {
        return $this->hasMany(AbsensiMahasiswa::class, 'pertemuan_kuliah_id');
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
     * Hitung persentase kehadiran
     */
    public function getPersentaseKehadiranAttribute()
    {
        $totalMahasiswa = $this->absensiMahasiswa()->count();
        if ($totalMahasiswa == 0) return 0;

        $hadir = $this->absensiMahasiswa()->where('status_kehadiran', 'Hadir')->count();
        return round(($hadir / $totalMahasiswa) * 100, 2);
    }

    /**
     * Hitung jumlah mahasiswa hadir
     */
    public function getJumlahHadirAttribute()
    {
        return $this->absensiMahasiswa()->where('status_kehadiran', 'Hadir')->count();
    }

    /**
     * Hitung jumlah mahasiswa tidak hadir
     */
    public function getJumlahTidakHadirAttribute()
    {
        return $this->absensiMahasiswa()->whereIn('status_kehadiran', ['Izin', 'Sakit', 'Alpa'])->count();
    }

    /**
     * Scope untuk pertemuan terjadwal
     */
    public function scopeTerjadwal($query)
    {
        return $query->where('status', 'Terjadwal');
    }

    /**
     * Scope untuk pertemuan selesai
     */
    public function scopeSelesai($query)
    {
        return $query->where('status', 'Selesai');
    }

    /**
     * Scope untuk pertemuan dibatalkan
     */
    public function scopeDibatalkan($query)
    {
        return $query->where('status', 'Dibatalkan');
    }
}
