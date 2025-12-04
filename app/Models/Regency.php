<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class Regency extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $fillable = [
        'province_id',
        'code',
        'name',
        'type',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relasi dengan Province
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    /**
     * Alias untuk relasi province (untuk backward compatibility)
     */
    public function provinsi(): BelongsTo
    {
        return $this->province();
    }

    /**
     * Relasi dengan SubRegencies
     */
    public function subRegencies(): HasMany
    {
        return $this->hasMany(SubRegency::class);
    }

    /**
     * Relasi polymorphic dengan files
     */
    public function files(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }
}
