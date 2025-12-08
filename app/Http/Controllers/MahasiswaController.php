<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mahasiswa::with(['programStudi.fakultas', 'user']);
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhere('nama_mahasiswa', 'like', "%{$search}%");
            });
        }
        
        $mahasiswa = $query->paginate(20);
        return view('mahasiswa.index', compact('mahasiswa'));
    }

    public function create()
    {
        $programStudi = ProgramStudi::all();
        return view('mahasiswa.create', compact('programStudi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_studi_id' => 'required|exists:program_studi,id',
            'nim' => 'required|unique:mahasiswa|max:20',
            'nama_mahasiswa' => 'required|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|max:50',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable',
            'telepon' => 'nullable|max:20',
            'email' => 'required|email|unique:mahasiswa|max:100',
            'tahun_masuk' => 'required|digits:4',
            'nama_wali' => 'nullable|max:100',
            'telepon_wali' => 'nullable|max:20'
        ]);

        Mahasiswa::create($validated);
        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil ditambahkan');
    }

    public function show(Mahasiswa $mahasiswa)
    {
        $mahasiswa->load(['programStudi.fakultas', 'krs.kelas.mataKuliah', 'krs.nilai', 'user.role']);
        return view('mahasiswa.show', compact('mahasiswa'));
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        $programStudi = ProgramStudi::all();
        return view('mahasiswa.edit', compact('mahasiswa', 'programStudi'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $validated = $request->validate([
            'program_studi_id' => 'required|exists:program_studi,id',
            'nim' => 'required|max:20|unique:mahasiswa,nim,' . $mahasiswa->id,
            'nama_mahasiswa' => 'required|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|max:50',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable',
            'telepon' => 'nullable|max:20',
            'email' => 'required|email|max:100|unique:mahasiswa,email,' . $mahasiswa->id,
            'tahun_masuk' => 'required|digits:4',
            'semester' => 'required|integer|min:1|max:14',
            'status' => 'required|in:Aktif,Cuti,Lulus,DO,Mengundurkan Diri',
            'nama_wali' => 'nullable|max:100',
            'telepon_wali' => 'nullable|max:20'
        ]);

        $mahasiswa->update($validated);
        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        $mahasiswa->deleted_by = 'System'; // Will be replaced by actual user when auth is implemented
        $mahasiswa->save();
        $mahasiswa->delete(); // Soft delete
        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil dihapus');
    }

    /**
     * Generate user account for mahasiswa
     */
    public function generateUser(Mahasiswa $mahasiswa)
    {
        // Check if mahasiswa already has user account
        if ($mahasiswa->user) {
            return redirect()->back()->with('error', 'Mahasiswa ini sudah memiliki akun user');
        }

        // Generate default password (format: Mhs + NIM)
        $defaultPassword = 'Mhs' . $mahasiswa->nim;

        try {
            // Get role mahasiswa
            $roleMahasiswa = \App\Models\Role::where('name', 'mahasiswa')->first();
            
            if (!$roleMahasiswa) {
                return redirect()->back()->with('error', 'Role mahasiswa tidak ditemukan');
            }

            // Create user account
            $user = \App\Models\User::create([
                'name' => $mahasiswa->nama_mahasiswa,
                'email' => $mahasiswa->email,
                'password' => bcrypt($defaultPassword),
                'role_id' => $roleMahasiswa->id,
                'fakultas_id' => $mahasiswa->programStudi->fakultas_id,
                'program_studi_id' => $mahasiswa->program_studi_id,
                'mahasiswa_id' => $mahasiswa->id,
                'is_active' => true,
                'created_by' => auth()->check() ? auth()->user()->name : 'System',
                'created_at' => now(),
            ]);

            // Return success with password info
            return redirect()->back()->with([
                'success' => 'Akun user berhasil dibuat',
                'generated_email' => $mahasiswa->email,
                'generated_password' => $defaultPassword,
                'show_credentials' => true
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat akun user: ' . $e->getMessage());
        }
    }
}
