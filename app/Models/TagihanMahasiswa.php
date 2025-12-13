<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Traits\AuditableTrait;

class TagihanMahasiswa extends Model
{
    use SoftDeletes, AuditableTrait;

    protected $table = 'tagihan_mahasiswa';

    protected $fillable = [
        'mahasiswa_id',
        'jenis_pembayaran_id',
        'tahun_akademik_id',
        'semester_id',
        'nomor_tagihan',
        'jumlah_tagihan',
        'jumlah_dibayar',
        'sisa_tagihan',
        'tanggal_tagihan',
        'tanggal_jatuh_tempo',
        'tanggal_lunas',
        'status',
        'denda',
        'diskon',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'jumlah_tagihan' => 'decimal:2',
        'jumlah_dibayar' => 'decimal:2',
        'sisa_tagihan' => 'decimal:2',
        'denda' => 'decimal:2',
        'diskon' => 'decimal:2',
        'tanggal_tagihan' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_lunas' => 'date',
    ];

    /**
     * Relasi ke mahasiswa
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    /**
     * Relasi ke jenis pembayaran
     */
    public function jenisPembayaran(): BelongsTo
    {
        return $this->belongsTo(JenisPembayaran::class, 'jenis_pembayaran_id');
    }

    /**
     * Relasi ke tahun akademik
     */
    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    /**
     * Relasi ke semester
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    /**
     * Relasi ke pembayaran
     */
    public function pembayaran(): HasMany
    {
        return $this->hasMany(PembayaranMahasiswa::class, 'tagihan_mahasiswa_id');
    }

    public function files(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'fileable')->orderBy('order');
    }

    /**
     * Scope untuk tagihan belum lunas
     */
    public function scopeBelumLunas($query)
    {
        return $query->whereIn('status', ['Belum Dibayar', 'Dibayar Sebagian']);
    }

    /**
     * Scope untuk tagihan lunas
     */
    public function scopeLunas($query)
    {
        return $query->where('status', 'Lunas');
    }

    /**
     * Scope untuk tagihan jatuh tempo
     */
    public function scopeJatuhTempo($query)
    {
        return $query->where('tanggal_jatuh_tempo', '<', now())
                    ->whereIn('status', ['Belum Dibayar', 'Dibayar Sebagian']);
    }

    /**
     * Update status tagihan berdasarkan pembayaran
     */
    public function updateStatus()
    {
        $sisaTagihan = $this->jumlah_tagihan - $this->jumlah_dibayar + $this->denda - $this->diskon;
        
        if ($sisaTagihan <= 0) {
            $this->status = 'Lunas';
            $this->tanggal_lunas = now();
            $this->sisa_tagihan = 0;
        } elseif ($this->jumlah_dibayar > 0) {
            $this->status = 'Dibayar Sebagian';
            $this->sisa_tagihan = $sisaTagihan;
        } else {
            $this->status = 'Belum Dibayar';
            $this->sisa_tagihan = $sisaTagihan;
        }
        
        $this->save();
    }

    /**
     * Generate nomor tagihan otomatis
     */
    public static function generateNomorTagihan()
    {
        $year = date('Y');
        $month = date('m');
        
        $lastTagihan = self::whereYear('created_at', $year)
                          ->whereMonth('created_at', $month)
                          ->orderBy('id', 'desc')
                          ->first();
        
        $lastNumber = $lastTagihan ? intval(substr($lastTagihan->nomor_tagihan, -5)) : 0;
        $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        
        return "TGH/{$year}/{$month}/{$newNumber}";
    }
}
