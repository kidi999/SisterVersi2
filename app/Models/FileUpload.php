<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AuditableTrait;

class FileUpload extends Model
{
    use SoftDeletes, AuditableTrait;

    protected $fillable = [
        'fileable_type',
        'fileable_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'description',
        'category',
        'order',
        'created_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the parent fileable model (Fakultas, Mahasiswa, etc).
     */
    public function fileable()
    {
        return $this->morphTo();
    }

    /**
     * Get original filename
     */
    public function getOriginalFilenameAttribute()
    {
        return $this->file_name;
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    /**
     * Get file extension
     */
    public function getExtensionAttribute()
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    /**
     * Check if file is an image
     */
    public function getIsImageAttribute()
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        return in_array(strtolower($this->extension), $imageExtensions);
    }

    /**
     * Get icon class based on file type
     */
    public function getIconClassAttribute()
    {
        $ext = strtolower($this->extension);
        
        $icons = [
            'pdf' => 'bi-file-pdf text-danger',
            'doc' => 'bi-file-word text-primary',
            'docx' => 'bi-file-word text-primary',
            'xls' => 'bi-file-excel text-success',
            'xlsx' => 'bi-file-excel text-success',
            'ppt' => 'bi-file-ppt text-warning',
            'pptx' => 'bi-file-ppt text-warning',
            'zip' => 'bi-file-zip text-secondary',
            'rar' => 'bi-file-zip text-secondary',
            'jpg' => 'bi-file-image text-info',
            'jpeg' => 'bi-file-image text-info',
            'png' => 'bi-file-image text-info',
            'gif' => 'bi-file-image text-info',
            'txt' => 'bi-file-text text-muted',
        ];

        return $icons[$ext] ?? 'bi-file-earmark text-secondary';
    }
}
