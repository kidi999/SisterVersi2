<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Traits\AuditableTrait;

class PembayaranMahasiswa extends Model
{
    use SoftDeletes, AuditableTrait;

    protected $table = 'pembayaran_mahasiswa';

    protected $fillable = [
        'tagihan_mahasiswa_id',
        'mahasiswa_id',
        'nomor_pembayaran',
        'jumlah_bayar',
        'tanggal_bayar',
        'waktu_bayar',
        'metode_pembayaran',
        'nama_bank',
        'nomor_rekening',
        'nama_pemilik_rekening',
        'nomor_referensi',
        'bukti_pembayaran',
        'status_verifikasi',
        'verified_by',
        'verified_at',
        'catatan_verifikasi',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'tanggal_bayar' => 'date',
        'verified_at' => 'datetime',
    ];

    /**
     * Relasi ke tagihan mahasiswa
     */
    public function tagihanMahasiswa(): BelongsTo
    {
        return $this->belongsTo(TagihanMahasiswa::class, 'tagihan_mahasiswa_id');
    }

    /**
     * Relasi ke mahasiswa
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    /**
     * Relasi ke user yang memverifikasi
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function files(): MorphMany
    {
        return $this->morphMany(FileUpload::class, 'fileable')->orderBy('order');
    }

    /**
     * Scope untuk pembayaran pending verifikasi
     */
    public function scopePendingVerifikasi($query)
    {
        return $query->where('status_verifikasi', 'Pending');
    }

    /**
     * Scope untuk pembayaran terverifikasi
     */
    public function scopeVerified($query)
    {
        return $query->where('status_verifikasi', 'Diverifikasi');
    }

    /**
     * Scope untuk pembayaran ditolak
     */
    public function scopeRejected($query)
    {
        return $query->where('status_verifikasi', 'Ditolak');
    }

    /**
     * Generate nomor pembayaran otomatis
     */
    public static function generateNomorPembayaran()
    {
        $year = date('Y');
        $month = date('m');
        
        $lastPembayaran = self::whereYear('created_at', $year)
                              ->whereMonth('created_at', $month)
                              ->orderBy('id', 'desc')
                              ->first();
        
        $lastNumber = $lastPembayaran ? intval(substr($lastPembayaran->nomor_pembayaran, -5)) : 0;
        $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        
        return "PMB/{$year}/{$month}/{$newNumber}";
    }

    /**
     * Update tagihan setelah pembayaran diverifikasi
     */
    public function updateTagihanAfterVerification()
    {
        if ($this->status_verifikasi === 'Diverifikasi') {
            $tagihan = $this->tagihanMahasiswa;
            $tagihan->jumlah_dibayar += $this->jumlah_bayar;
            $tagihan->updateStatus();
        }
    }
}
