<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class ProgramStudi extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $table = 'program_studi';

    protected $fillable = [
        'fakultas_id',
        'kode_prodi',
        'nama_prodi',
        'jenjang',
        'kaprodi',
        'akreditasi',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $dates = ['deleted_at', 'created_at'];

    /**
     * Relasi dengan Fakultas
     */
    public function fakultas(): BelongsTo
    {
        return $this->belongsTo(Fakultas::class);
    }

    /**
     * Relasi dengan Mahasiswa
     */
    public function mahasiswa(): HasMany
    {
        return $this->hasMany(Mahasiswa::class);
    }

    /**
     * Relasi dengan Dosen
     */
    public function dosen(): HasMany
    {
        return $this->hasMany(Dosen::class);
    }

    /**
     * Relasi dengan Mata Kuliah
     */
    public function mataKuliah(): HasMany
    {
        return $this->hasMany(MataKuliah::class);
    }

    /**
     * Relasi dengan Users (One-to-Many)
     * Users yang terdaftar di program studi ini (admin_prodi, dll)
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relasi polymorphic dengan FileUpload
     */
    public function files()
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }
}
