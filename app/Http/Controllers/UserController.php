<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\FileUpload;
use App\Support\TabularExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $users = User::with(['role', 'fakultas', 'programStudi', 'dosen', 'mahasiswa'])
            ->where('id', '!=', 1) // Exclude super admin default
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    public function exportExcel(Request $request)
    {
        $users = User::with(['role', 'fakultas', 'programStudi', 'dosen', 'mahasiswa'])
            ->where('id', '!=', 1)
            ->orderBy('name')
            ->get();

        $headers = [
            'Nama',
            'Email',
            'Role',
            'Fakultas',
            'Program Studi',
            'Status',
        ];

        $rows = $users->map(function ($user) {
            return [
                $user->name,
                $user->email,
                $user->role->display_name ?? '-',
                $user->fakultas->nama_fakultas ?? '-',
                $user->programStudi->nama_prodi ?? '-',
                $user->is_active ? 'Aktif' : 'Nonaktif',
            ];
        });

        $html = TabularExport::htmlTable($headers, $rows);
        return TabularExport::excelResponse('users.xls', $html);
    }

    public function exportPdf(Request $request)
    {
        $users = User::with(['role', 'fakultas', 'programStudi', 'dosen', 'mahasiswa'])
            ->where('id', '!=', 1)
            ->orderBy('name')
            ->get();

        $headers = [
            'Nama',
            'Email',
            'Role',
            'Fakultas',
            'Program Studi',
            'Status',
        ];

        $rows = $users->map(function ($user) {
            return [
                $user->name,
                $user->email,
                $user->role->display_name ?? '-',
                $user->fakultas->nama_fakultas ?? '-',
                $user->programStudi->nama_prodi ?? '-',
                $user->is_active ? 'Aktif' : 'Nonaktif',
            ];
        });

        $html = TabularExport::htmlTable($headers, $rows);
        return Pdf::loadHTML($html)->download('users.pdf');
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::orderBy('display_name')->get();
        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        
        return view('users.create', compact('roles', 'fakultas'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'fakultas_id' => 'nullable|exists:fakultas,id',
            'program_studi_id' => 'nullable|exists:program_studi,id',
            'dosen_id' => 'nullable|exists:dosen,id',
            'mahasiswa_id' => 'nullable|exists:mahasiswa,id',
        ], [
            'name.required' => 'Nama harus diisi',
            'name.max' => 'Nama maksimal 255 karakter',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role_id.required' => 'Role harus dipilih',
            'role_id.exists' => 'Role tidak valid',
            'fakultas_id.exists' => 'Fakultas tidak valid',
            'program_studi_id.exists' => 'Program Studi tidak valid',
            'dosen_id.exists' => 'Dosen tidak valid',
            'mahasiswa_id.exists' => 'Mahasiswa tidak valid',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['created_by'] = Auth::user()->name;

        $user = User::create($validated);

        // Handle file uploads
        if ($request->has('file_ids')) {
            $fileIds = is_array($request->file_ids) ? $request->file_ids : json_decode($request->file_ids, true);
            if (is_array($fileIds) && count($fileIds) > 0) {
                FileUpload::whereIn('id', $fileIds)->update([
                    'fileable_id' => $user->id,
                    'fileable_type' => User::class,
                ]);
            }
        }

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['role', 'fakultas', 'programStudi', 'dosen', 'mahasiswa', 'files']);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = Role::orderBy('display_name')->get();
        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        $programStudis = $user->fakultas_id 
            ? ProgramStudi::where('fakultas_id', $user->fakultas_id)->orderBy('nama_prodi')->get() 
            : [];
        
        // Load existing files
        $user->load('files');
        
        return view('users.edit', compact('user', 'roles', 'fakultas', 'programStudis'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'fakultas_id' => 'nullable|exists:fakultas,id',
            'program_studi_id' => 'nullable|exists:program_studi,id',
            'dosen_id' => 'nullable|exists:dosen,id',
            'mahasiswa_id' => 'nullable|exists:mahasiswa,id',
        ], [
            'name.required' => 'Nama harus diisi',
            'name.max' => 'Nama maksimal 255 karakter',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role_id.required' => 'Role harus dipilih',
            'role_id.exists' => 'Role tidak valid',
            'fakultas_id.exists' => 'Fakultas tidak valid',
            'program_studi_id.exists' => 'Program Studi tidak valid',
            'dosen_id.exists' => 'Dosen tidak valid',
            'mahasiswa_id.exists' => 'Mahasiswa tidak valid',
        ]);

        // Update password only if provided
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['updated_by'] = Auth::user()->name;

        $user->update($validated);

        // Handle file upload
        if ($request->has('file_ids')) {
            $fileIds = json_decode($request->file_ids, true);
            if (is_array($fileIds) && count($fileIds) > 0) {
                // Update existing files to link to this user
                FileUpload::whereIn('id', $fileIds)->update([
                    'fileable_id' => $user->id,
                    'fileable_type' => User::class,
                ]);

                // Remove orphaned files (files that were uploaded but removed from the list)
                $orphanedFiles = FileUpload::where('fileable_type', User::class)
                    ->where('fileable_id', $user->id)
                    ->whereNotIn('id', $fileIds)
                    ->get();

                foreach ($orphanedFiles as $file) {
                    \Storage::disk('public')->delete($file->file_path);
                    $file->delete();
                }
            }
        } else {
            // If no files selected, remove all files
            $allFiles = FileUpload::where('fileable_type', User::class)
                ->where('fileable_id', $user->id)
                ->get();

            foreach ($allFiles as $file) {
                \Storage::disk('public')->delete($file->file_path);
                $file->delete();
            }
        }

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage (soft delete).
     */
    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        // Prevent deleting default super admin
        if ($user->id === 1) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus Super Admin default.');
        }

        $user->deleted_by = Auth::user()->name;
        $user->save();
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Display a listing of trashed users (super_admin only).
     */
    public function trash()
    {
        if (!Auth::user()->hasRole(['super_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::onlyTrashed()
            ->with(['role'])
            ->get();

        return view('users.trash', compact('users'));
    }

    /**
     * Restore the specified user from trash (super_admin only).
     */
    public function restore(string $id)
    {
        if (!Auth::user()->hasRole(['super_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('users.trash')
            ->with('success', 'User berhasil dipulihkan.');
    }

    /**
     * Permanently delete the specified user (super_admin only).
     */
    public function forceDelete(string $id)
    {
        if (!Auth::user()->hasRole(['super_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $user = User::onlyTrashed()->findOrFail($id);
        
        // Prevent force deleting default super admin
        if ($user->id === 1) {
            return redirect()->route('users.trash')
                ->with('error', 'Tidak dapat menghapus permanen Super Admin default.');
        }

        $user->forceDelete();

        return redirect()->route('users.trash')
            ->with('success', 'User berhasil dihapus permanen.');
    }

    /**
     * Toggle user active status.
     */
    public function toggleActive(User $user)
    {
        // Prevent deactivating own account
        if ($user->id === Auth::id()) {
            return response()->json(['error' => 'Tidak dapat menonaktifkan akun sendiri.'], 403);
        }

        $user->is_active = !$user->is_active;
        $user->updated_by = Auth::user()->name;
        $user->save();

        return response()->json([
            'success' => true,
            'is_active' => $user->is_active,
            'message' => $user->is_active ? 'User diaktifkan.' : 'User dinonaktifkan.'
        ]);
    }

    /**
     * Get program studi by fakultas (AJAX).
     */
    public function getProgramStudi($fakultasId)
    {
        $programStudis = ProgramStudi::where('fakultas_id', $fakultasId)
            ->orderBy('nama_prodi')
            ->get(['id', 'nama_prodi']);

        return response()->json($programStudis);
    }
}
