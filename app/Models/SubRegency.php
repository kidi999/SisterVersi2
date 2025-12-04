<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class SubRegency extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $fillable = [
        'regency_id',
        'code',
        'name',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    /**
     * Relasi dengan Regency
     */
    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }

    /**
     * Relasi dengan Villages
     */
    public function villages(): HasMany
    {
        return $this->hasMany(Village::class);
    }

    /**
     * Relasi polymorphic dengan FileUpload
     */
    public function files()
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }
}
