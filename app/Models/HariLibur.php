<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\AuditableTrait;

class HariLibur extends Model
{
    use SoftDeletes, AuditableTrait;

    protected $table = 'hari_libur';

    protected $fillable = [
        'nama',
        'tanggal',
        'jenis',
        'keterangan',
        'is_recurring',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_recurring' => 'boolean',
    ];

    /**
     * Relasi ke User - Created By
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke User - Updated By
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relasi ke User - Deleted By
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Check if a date is holiday
     */
    public static function isHoliday($date)
    {
        return self::whereDate('tanggal', $date)->exists();
    }

    /**
     * Get holidays in date range
     */
    public static function getHolidaysInRange($startDate, $endDate)
    {
        return self::whereBetween('tanggal', [$startDate, $endDate])
                   ->orderBy('tanggal')
                   ->get();
    }

    /**
     * Scope untuk jenis tertentu
     */
    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    /**
     * Scope untuk tahun tertentu
     */
    public function scopeByYear($query, $year)
    {
        return $query->whereYear('tanggal', $year);
    }
}
