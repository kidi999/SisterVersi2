# File Upload System - Implementation Summary

## Overview
Sistem file upload telah berhasil diimplementasikan untuk modul Fakultas dengan kemampuan:
- Upload multiple files (opsional: 0 atau lebih file)
- AJAX-based upload untuk dynamic add/remove
- Maksimal ukuran file: 3 MB per file
- Polymorphic relationship untuk reusability di semua modul

## File-file yang Dibuat/Diupdate

### 1. Database Migration
**File:** `database/migrations/2024_12_01_230000_create_file_uploads_table.php`
- Tabel polymorphic untuk menyimpan file dari berbagai model
- Fields: fileable_type, fileable_id, file_name, file_path, file_type, file_size
- Audit fields: inserted_by, inserted_at, updated_by, updated_at, deleted_by, deleted_at
- Soft delete enabled

### 2. Model FileUpload
**File:** `app/Models/FileUpload.php`
- Polymorphic relationship: `morphTo('fileable')`
- Helper methods:
  * `formatted_size` - Format ukuran file (MB/KB/bytes)
  * `extension` - Get ekstensi file
  * `is_image` - Boolean untuk cek apakah file adalah gambar
  * `icon_class` - Bootstrap icon class berdasarkan tipe file

### 3. Controller FileUpload
**File:** `app/Http/Controllers/FileUploadController.php`
- `upload()` - Upload file via AJAX, validasi max 3072KB
- `destroy()` - Soft delete file dan hapus file fisik
- `download()` - Download file
- `getFiles()` - Get list files untuk entity tertentu

### 4. Routes
**File:** `routes/web.php`
```php
Route::post('api/file-upload', [FileUploadController::class, 'upload']);
Route::delete('api/file-upload/{id}', [FileUploadController::class, 'destroy']);
Route::get('api/file-upload/{id}/download', [FileUploadController::class, 'download']);
Route::get('api/file-upload/get-files', [FileUploadController::class, 'getFiles']);
```

### 5. File Upload Component
**File:** `resources/views/components/file-upload.blade.php`
- Reusable component untuk semua modul
- Features:
  * Upload button dengan file input
  * AJAX upload dengan progress indicator
  * File list dengan preview (nama, ukuran, icon)
  * Delete button per file
  * Hidden input untuk file_ids array
  * Load existing files (untuk edit mode)

### 6. Updated Fakultas Files

#### Model
**File:** `app/Models/Fakultas.php`
- Added relationship: `morphMany(FileUpload::class, 'fileable')`

#### Controller
**File:** `app/Http/Controllers/FakultasController.php`
- `store()`: Handle file_ids JSON array, update FileUpload records
- `edit()`: Eager load 'files' relationship
- `show()`: Eager load 'files' relationship
- `update()`: Handle file_ids untuk update associations

#### Views
**Files:**
- `resources/views/fakultas/create.blade.php` - Added file upload component
- `resources/views/fakultas/edit.blade.php` - Added file upload component with existing files
- `resources/views/fakultas/show.blade.php` - Display uploaded files with download links

## Cara Penggunaan

### Di View (Create/Edit)
```blade
@include('components.file-upload', [
    'fileableType' => 'App\\Models\\Fakultas',
    'fileableId' => $fakultas->id ?? 0,
    'existingFiles' => $fakultas->files ?? []
])
```

### Di Controller (Store/Update)
```php
// Validasi
'file_ids' => 'nullable|json'

// Setelah create/update entity
if ($request->has('file_ids')) {
    $fileIds = json_decode($request->file_ids, true);
    if (is_array($fileIds) && count($fileIds) > 0) {
        FileUpload::whereIn('id', $fileIds)->update([
            'fileable_id' => $entity->id,
            'fileable_type' => 'App\Models\EntityName'
        ]);
    }
}
```

### Di Model
```php
public function files()
{
    return $this->morphMany(FileUpload::class, 'fileable')->orderBy('order');
}
```

## Storage Configuration
- Files disimpan di: `storage/app/public/uploads/{entity_type}/`
- Public access via: `public/storage/` (symlink created)
- Command untuk create symlink: `php artisan storage:link`

## Validasi
- Max file size: 3 MB (3072 KB)
- Allowed file types: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG, GIF
- Validation dilakukan di controller dan JavaScript

## File Icons (Bootstrap Icons)
- PDF: `bi-file-earmark-pdf-fill text-danger`
- Word: `bi-file-earmark-word-fill text-primary`
- Excel: `bi-file-earmark-excel-fill text-success`
- Image: `bi-file-earmark-image-fill text-info`
- Default: `bi-file-earmark-fill text-secondary`

## JavaScript Features
- AJAX upload tanpa refresh page
- Dynamic add/remove files
- File size validation
- Upload progress indicator
- File preview dengan info (nama, ukuran, tanggal, uploader)
- Delete confirmation

## Security
- CSRF token validation
- File type validation
- File size validation
- Authentication required
- Unique filename dengan timestamp

## Testing Checklist
- [x] Migration berhasil dijalankan
- [x] Storage link created
- [x] Routes registered
- [ ] Upload file di create form
- [ ] Upload file di edit form
- [ ] View files di show page
- [ ] Download file
- [ ] Delete file
- [ ] Multiple file upload
- [ ] File validation (size & type)

## Next Steps untuk Modul Lain
1. Tambahkan relationship di model:
   ```php
   public function files() {
       return $this->morphMany(FileUpload::class, 'fileable')->orderBy('order');
   }
   ```

2. Update controller store/update methods untuk handle file_ids

3. Include file upload component di create/edit views

4. Display files di show view

5. Eager load 'files' di edit dan show methods

## Notes
- Setiap entity bisa punya 0 atau lebih files
- Files ter-link via polymorphic relationship
- Soft delete file akan hapus file fisik juga
- Files diurutkan berdasarkan field 'order'
- Semua file operations tercatat di audit fields

---
**Status:** ✅ Implemented and Ready for Testing
**Date:** 2024-12-01
**Module:** Fakultas (dapat direplikasi ke modul lain)
