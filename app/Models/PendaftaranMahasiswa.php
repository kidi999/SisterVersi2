<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class PendaftaranMahasiswa extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'pendaftaran_mahasiswa';

    protected $fillable = [
        'tahun_akademik',
        'jalur_masuk',
        'program_studi_id',
        'no_pendaftaran',
        'nama_lengkap',
        'nik',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'status_perkawinan',
        'kewarganegaraan',
        'alamat',
        'village_id',
        'kode_pos',
        'telepon',
        'email',
        'email_verification_token',
        'email_verified_at',
        'asal_sekolah',
        'jurusan_sekolah',
        'tahun_lulus',
        'nilai_rata_rata',
        'nama_ayah',
        'pekerjaan_ayah',
        'nama_ibu',
        'pekerjaan_ibu',
        'nama_wali',
        'telepon_wali',
        'alamat_wali',
        'status',
        'catatan',
        'tanggal_verifikasi',
        'verifikasi_by',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_daftar' => 'datetime',
        'tanggal_verifikasi' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relasi dengan Program Studi
     */
    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    /**
     * Relasi dengan Village (Desa/Kelurahan)
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    /**
     * Relasi dengan User - Verifikator
     */
    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifikasi_by');
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
     * Relasi polymorphic dengan FileUpload
     */
    public function files(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }

    /**
     * Accessor untuk badge status
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'Pending' => 'warning',
            'Diverifikasi' => 'info',
            'Diterima' => 'success',
            'Ditolak' => 'danger',
            'Dieksport' => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Accessor untuk badge jalur masuk
     */
    public function getJalurBadgeAttribute(): string
    {
        return match($this->jalur_masuk) {
            'SNBP' => 'primary',
            'SNBT' => 'info',
            'Mandiri' => 'success',
            'Transfer' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Generate nomor pendaftaran otomatis
     */
    public static function generateNoPendaftaran($tahun_akademik, $program_studi_id)
    {
        $year = substr($tahun_akademik, 0, 4);
        $prodi = ProgramStudi::find($program_studi_id);
        $kode_prodi = $prodi ? str_pad($prodi->id, 3, '0', STR_PAD_LEFT) : '000';
        
        // Hitung jumlah pendaftaran tahun ini untuk prodi ini
        $count = self::where('tahun_akademik', $tahun_akademik)
                    ->where('program_studi_id', $program_studi_id)
                    ->count();
        
        $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        
        return "PMB{$year}{$kode_prodi}{$sequence}";
    }
}
