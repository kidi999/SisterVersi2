<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class Role extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $fillable = [
        'name',
        'display_name',
        'description',
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
     * Relasi dengan Users
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Role constants
    const SUPER_ADMIN = 'super_admin';
    const ADMIN_UNIVERSITAS = 'admin_universitas';
    const ADMIN_FAKULTAS = 'admin_fakultas';
    const ADMIN_PRODI = 'admin_prodi';
    const DOSEN = 'dosen';
    const MAHASISWA = 'mahasiswa';
}
