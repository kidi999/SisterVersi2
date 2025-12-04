<?php

namespace App\Http\Controllers;

use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    /**
     * Upload file via AJAX
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:3072', // Max 3MB
            'fileable_type' => 'required|string',
            'fileable_id' => 'nullable',
            'category' => 'nullable|string',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $file = $request->file('file');
            
            // Generate unique filename
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            
            // Get entity type for folder name (without backslashes)
            $entityFolder = strtolower(class_basename($request->fileable_type));
            
            // Store file
            $path = $file->storeAs('uploads/' . $entityFolder, $fileName, 'public');
            
            // Create file record
            $fileUpload = new FileUpload([
                'fileable_type' => $request->fileable_type,
                'fileable_id' => $request->fileable_id ?? 0,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'description' => $request->description,
                'category' => $request->category ?? 'general',
                'order' => 0,
            ]);
            
            $fileUpload->created_by = Auth::user()->name;
            $fileUpload->save();
            
            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload',
                'file' => [
                    'id' => $fileUpload->id,
                    'name' => $fileUpload->file_name,
                    'size' => $fileUpload->formatted_size,
                    'icon' => $fileUpload->icon_class,
                    'is_image' => $fileUpload->is_image,
                    'url' => asset('storage/' . $fileUpload->file_path),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete uploaded file
     */
    public function destroy(Request $request, $id)
    {
        try {
            $file = FileUpload::findOrFail($id);
            
            // Delete physical file
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            
            // Soft delete record
            $file->deleted_by = Auth::user()->name;
            $file->save();
            $file->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'File berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download file
     */
    public function download($id)
    {
        try {
            $file = FileUpload::findOrFail($id);
            
            if (!Storage::disk('public')->exists($file->file_path)) {
                abort(404, 'File tidak ditemukan');
            }
            
            return Storage::disk('public')->download($file->file_path, $file->file_name);
        } catch (\Exception $e) {
            abort(404, 'File tidak ditemukan');
        }
    }

    /**
     * Get files for an entity
     */
    public function getFiles(Request $request)
    {
        $request->validate([
            'fileable_type' => 'required|string',
            'fileable_id' => 'required|integer',
        ]);

        $files = FileUpload::where('fileable_type', $request->fileable_type)
            ->where('fileable_id', $request->fileable_id)
            ->orderBy('order')
            ->get()
            ->map(function ($file) {
                return [
                    'id' => $file->id,
                    'name' => $file->file_name,
                    'size' => $file->formatted_size,
                    'icon' => $file->icon_class,
                    'is_image' => $file->is_image,
                    'description' => $file->description,
                    'url' => asset('storage/' . $file->file_path),
                    'uploaded_by' => $file->inserted_by,
                    'uploaded_at' => $file->inserted_at ? $file->inserted_at->format('d/m/Y H:i') : '-',
                ];
            });

        return response()->json([
            'success' => true,
            'files' => $files
        ]);
    }
}
