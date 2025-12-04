<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class Village extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $fillable = [
        'sub_regency_id',
        'code',
        'name',
        'postal_code',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    /**
     * Relasi dengan SubRegency
     */
    public function subRegency(): BelongsTo
    {
        return $this->belongsTo(SubRegency::class);
    }

    /**
     * Relasi dengan Fakultas
     */
    public function fakultas(): HasMany
    {
        return $this->hasMany(Fakultas::class);
    }

    /**
     * Relasi dengan Dosen
     */
    public function dosen(): HasMany
    {
        return $this->hasMany(Dosen::class);
    }

    /**
     * Relasi dengan Mahasiswa
     */
    public function mahasiswa(): HasMany
    {
        return $this->hasMany(Mahasiswa::class);
    }

    /**
     * Relasi polymorphic dengan FileUpload
     */
    public function files()
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }

    /**
     * Get full address with hierarchy
     */
    public function getFullAddressAttribute(): string
    {
        return $this->name . ', ' . 
               $this->subRegency->name . ', ' . 
               $this->subRegency->regency->name . ', ' . 
               $this->subRegency->regency->province->name;
    }
}
