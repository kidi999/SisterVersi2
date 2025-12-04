<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class University extends Model
{
    use HasFactory, SoftDeletes, AuditableTrait;

    protected $fillable = [
        'kode',
        'nama',
        'singkatan',
        'jenis',
        'status',
        'akreditasi',
        'no_sk_akreditasi',
        'tanggal_akreditasi',
        'tanggal_berakhir_akreditasi',
        'no_sk_pendirian',
        'tanggal_pendirian',
        'no_izin_operasional',
        'tanggal_izin_operasional',
        'rektor',
        'nip_rektor',
        'wakil_rektor_1',
        'wakil_rektor_2',
        'wakil_rektor_3',
        'wakil_rektor_4',
        'email',
        'telepon',
        'fax',
        'website',
        'alamat',
        'village_id',
        'kode_pos',
        'logo_path',
        'nama_file_logo',
        'visi',
        'misi',
        'sejarah',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $dates = ['deleted_at', 'tanggal_akreditasi', 'tanggal_berakhir_akreditasi', 'tanggal_pendirian', 'tanggal_izin_operasional'];

    /**
     * Relasi dengan Village
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    /**
     * Relasi polymorphic dengan FileUpload
     */
    public function files()
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }

    /**
     * Get logo URL
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo_path) {
            return \Storage::url($this->logo_path);
        }
        return asset('images/default-university-logo.png');
    }
}
