<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class Dosen extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'dosen';

    protected $fillable = [
        'level_dosen',
        'fakultas_id',
        'program_studi_id',
        'nip',
        'nidn',
        'nama_dosen',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'village_id',
        'telepon',
        'email',
        'pendidikan_terakhir',
        'jabatan_akademik',
        'status',
        'inserted_by',
        'inserted_at',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date'
    ];

    protected $dates = ['deleted_at', 'inserted_at'];

    /**
     * Relasi dengan Village
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    /**
     * Relasi dengan Fakultas
     */
    public function fakultas(): BelongsTo
    {
        return $this->belongsTo(Fakultas::class);
    }

    /**
     * Relasi dengan Program Studi
     */
    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class);
    }

    /**
     * Relasi dengan Kelas
     */
    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }

    /**
     * Relasi dengan Jabatan Struktural
     */
    public function jabatanStruktural(): HasMany
    {
        return $this->hasMany(JabatanStruktural::class)->orderBy('tanggal_mulai', 'desc');
    }

    /**
     * Get jabatan struktural yang sedang aktif
     */
    public function jabatanAktif()
    {
        return $this->hasMany(JabatanStruktural::class)->where('status', 'aktif');
    }

    /**
     * Get current jabatan label
     */
    public function getCurrentJabatanAttribute()
    {
        $jabatan = $this->jabatanAktif()->first();
        return $jabatan ? $jabatan->nama_jabatan : null;
    }

    /**
     * Relasi polymorphic dengan FileUpload
     */
    public function files()
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }

    /**
     * Status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'Aktif' => 'success',
            'Non-Aktif' => 'danger',
            'Cuti' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Level badge color
     */
    public function getLevelBadgeAttribute()
    {
        return match($this->level_dosen) {
            'universitas' => 'primary',
            'fakultas' => 'info',
            'prodi' => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Level label
     */
    public function getLevelLabelAttribute()
    {
        return match($this->level_dosen) {
            'universitas' => 'Dosen Universitas',
            'fakultas' => 'Dosen Fakultas',
            'prodi' => 'Dosen Program Studi',
            default => '-'
        };
    }

    /**
     * Get scope label based on level
     */
    public function getScopeLabelAttribute()
    {
        return match($this->level_dosen) {
            'universitas' => 'Semua Fakultas & Program Studi',
            'fakultas' => $this->fakultas->nama_fakultas ?? 'Fakultas',
            'prodi' => ($this->programStudi->nama_prodi ?? 'Program Studi') . ' - ' . ($this->programStudi->fakultas->nama_fakultas ?? ''),
            default => '-'
        };
    }

    /**
     * Relasi dengan User (One-to-One)
     * Satu dosen memiliki satu akun user
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }
}
