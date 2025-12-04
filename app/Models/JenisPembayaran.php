<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\AuditableTrait;

class JenisPembayaran extends Model
{
    use SoftDeletes, AuditableTrait;

    protected $table = 'jenis_pembayaran';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'kategori',
        'is_wajib',
        'is_active',
        'urutan',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'is_wajib' => 'boolean',
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    /**
     * Relasi ke tagihan mahasiswa
     */
    public function tagihanMahasiswa(): HasMany
    {
        return $this->hasMany(TagihanMahasiswa::class, 'jenis_pembayaran_id');
    }

    /**
     * Scope untuk jenis pembayaran aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk jenis pembayaran wajib
     */
    public function scopeWajib($query)
    {
        return $query->where('is_wajib', true);
    }

    /**
     * Scope berdasarkan kategori
     */
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }
}
