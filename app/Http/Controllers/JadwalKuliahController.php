<?php

namespace App\Http\Controllers;

use App\Models\JadwalKuliah;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\Ruang;
use App\Models\TahunAkademik;
use App\Models\Semester;
use App\Models\ProgramStudi;
use App\Models\Fakultas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class JadwalKuliahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = JadwalKuliah::with([
            'kelas.mataKuliah.fakultas',
            'kelas.mataKuliah.programStudi',
            'kelas.dosen',
            'ruang',
            'tahunAkademik',
            'semester'
        ]);

        // Filter by tahun akademik
        if ($request->filled('tahun_akademik_id')) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }

        // Filter by semester
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        // Filter by hari
        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        // Filter by ruang
        if ($request->filled('ruang_id')) {
            $query->where('ruang_id', $request->ruang_id);
        }

        // Filter by mata kuliah
        if ($request->filled('mata_kuliah_id')) {
            $query->whereHas('kelas', function($q) use ($request) {
                $q->where('mata_kuliah_id', $request->mata_kuliah_id);
            });
        }

        // Filter by fakultas
        if ($request->filled('fakultas_id')) {
            $query->whereHas('kelas.mataKuliah', function($q) use ($request) {
                $q->where('fakultas_id', $request->fakultas_id)
                  ->orWhere('level_matkul', 'universitas');
            });
        }

        // Filter by prodi
        if ($request->filled('program_studi_id')) {
            $prodi = ProgramStudi::find($request->program_studi_id);
            if ($prodi) {
                $query->whereHas('kelas.mataKuliah', function($q) use ($request, $prodi) {
                    $q->where(function($q2) use ($request, $prodi) {
                        $q2->where('program_studi_id', $request->program_studi_id)
                           ->orWhere(function($q3) use ($prodi) {
                               $q3->where('level_matkul', 'fakultas')
                                  ->where('fakultas_id', $prodi->fakultas_id);
                           })
                           ->orWhere('level_matkul', 'universitas');
                    });
                });
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('kelas.mataKuliah', function($q) use ($search) {
                $q->where('nama_mk', 'like', "%{$search}%")
                  ->orWhere('kode_mk', 'like', "%{$search}%");
            })->orWhereHas('kelas.dosen', function($q) use ($search) {
                $q->where('nama_dosen', 'like', "%{$search}%");
            });
        }

        $jadwal = $query->orderBy('hari')->orderBy('jam_mulai')->paginate(20);

        // Data for filters
        $tahunAkademik = TahunAkademik::orderBy('tahun_mulai', 'desc')->get();
        $semesters = Semester::with('tahunAkademik')->orderBy('tanggal_mulai', 'desc')->get();
        $ruang = Ruang::orderBy('kode_ruang')->get();
        $mataKuliah = MataKuliah::with(['fakultas', 'programStudi'])->orderBy('nama_mk')->get();
        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        $programStudi = ProgramStudi::with('fakultas')->orderBy('nama_prodi')->get();

        return view('jadwal-kuliah.index', compact(
            'jadwal', 
            'tahunAkademik', 
            'semesters', 
            'ruang', 
            'mataKuliah',
            'fakultas',
            'programStudi'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get kelas based on user access level
        $kelasQuery = Kelas::with(['mataKuliah.fakultas', 'mataKuliah.programStudi', 'dosen']);
        
        // Apply access control based on user role
        if ($user->role && $user->role->name === 'admin_prodi' && $user->program_studi_id) {
            $prodi = ProgramStudi::find($user->program_studi_id);
            $kelasQuery->whereHas('mataKuliah', function($q) use ($user, $prodi) {
                $q->where('program_studi_id', $user->program_studi_id)
                  ->orWhere(function($q2) use ($prodi) {
                      $q2->where('level_matkul', 'fakultas')
                         ->where('fakultas_id', $prodi->fakultas_id);
                  })
                  ->orWhere('level_matkul', 'universitas');
            });
        } elseif ($user->role && $user->role->name === 'admin_fakultas' && $user->fakultas_id) {
            $kelasQuery->whereHas('mataKuliah', function($q) use ($user) {
                $q->where('fakultas_id', $user->fakultas_id)
                  ->orWhere('level_matkul', 'universitas');
            });
        }
        
        $kelas = $kelasQuery->orderBy('kode_kelas')->get();
        $ruang = Ruang::where('status', 'Aktif')->orderBy('kode_ruang')->get();
        $tahunAkademik = TahunAkademik::where('is_active', true)->orderBy('tahun_mulai', 'desc')->get();
        $semesters = Semester::where('is_active', true)->orderBy('tanggal_mulai', 'desc')->get();

        return view('jadwal-kuliah.create', compact('kelas', 'ruang', 'tahunAkademik', 'semesters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'kelas_id' => 'required|exists:kelas,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'semester_id' => 'required|exists:semesters,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruang_id' => 'required|exists:ruang,id',
        ];

        $validated = $request->validate($rules);

        // Check ruang conflict
        $ruangConflict = JadwalKuliah::checkRuangConflict(
            $validated['ruang_id'],
            $validated['hari'],
            $validated['jam_mulai'],
            $validated['jam_selesai']
        );

        if ($ruangConflict) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ruangan bentrok dengan jadwal: ' . 
                    $ruangConflict->kelas->mataKuliah->nama_mk . 
                    ' (' . $ruangConflict->kelas->dosen->nama_dosen . ') pada ' . 
                    $ruangConflict->hari . ' jam ' . $ruangConflict->waktu);
        }

        // Check dosen conflict
        $dosenConflict = JadwalKuliah::checkDosenConflict(
            $validated['kelas_id'],
            $validated['hari'],
            $validated['jam_mulai'],
            $validated['jam_selesai']
        );

        if ($dosenConflict) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Dosen bentrok dengan jadwal: ' . 
                    $dosenConflict->kelas->mataKuliah->nama_mk . 
                    ' pada ' . $dosenConflict->hari . ' jam ' . $dosenConflict->waktu);
        }

        // Check if ruang can be used by this kelas
        $kelas = Kelas::with('mataKuliah.programStudi')->find($validated['kelas_id']);
        $ruang = Ruang::find($validated['ruang_id']);
        
        if ($kelas && $kelas->mataKuliah && $kelas->mataKuliah->programStudi) {
            $canUse = $ruang->canBeUsedBy(
                $kelas->mataKuliah->program_studi_id,
                $kelas->mataKuliah->programStudi->fakultas_id
            );
            
            if (!$canUse) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Ruangan ini tidak dapat digunakan oleh program studi ' . 
                        $kelas->mataKuliah->programStudi->nama_prodi);
            }
        }

        DB::beginTransaction();
        try {
            JadwalKuliah::create($validated);

            DB::commit();
            return redirect()->route('jadwal-kuliah.index')
                ->with('success', 'Jadwal kuliah berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(JadwalKuliah $jadwalKuliah)
    {
        $jadwalKuliah->load([
            'kelas.mataKuliah.fakultas',
            'kelas.mataKuliah.programStudi',
            'kelas.dosen',
            'ruang',
            'tahunAkademik',
            'semester',
            'createdBy',
            'updatedBy'
        ]);

        return view('jadwal-kuliah.show', compact('jadwalKuliah'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JadwalKuliah $jadwalKuliah)
    {
        $jadwalKuliah->load('kelas.mataKuliah');
        
        $user = Auth::user();
        
        // Get kelas based on user access level
        $kelasQuery = Kelas::with(['mataKuliah.fakultas', 'mataKuliah.programStudi', 'dosen']);
        
        if ($user->role && $user->role->name === 'admin_prodi' && $user->program_studi_id) {
            $prodi = ProgramStudi::find($user->program_studi_id);
            $kelasQuery->whereHas('mataKuliah', function($q) use ($user, $prodi) {
                $q->where('program_studi_id', $user->program_studi_id)
                  ->orWhere(function($q2) use ($prodi) {
                      $q2->where('level_matkul', 'fakultas')
                         ->where('fakultas_id', $prodi->fakultas_id);
                  })
                  ->orWhere('level_matkul', 'universitas');
            });
        } elseif ($user->role && $user->role->name === 'admin_fakultas' && $user->fakultas_id) {
            $kelasQuery->whereHas('mataKuliah', function($q) use ($user) {
                $q->where('fakultas_id', $user->fakultas_id)
                  ->orWhere('level_matkul', 'universitas');
            });
        }
        
        $kelas = $kelasQuery->orderBy('kode_kelas')->get();
        $ruang = Ruang::where('status', 'Aktif')->orderBy('kode_ruang')->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun_mulai', 'desc')->get();
        $semesters = Semester::orderBy('tanggal_mulai', 'desc')->get();

        return view('jadwal-kuliah.edit', compact('jadwalKuliah', 'kelas', 'ruang', 'tahunAkademik', 'semesters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JadwalKuliah $jadwalKuliah)
    {
        $rules = [
            'kelas_id' => 'required|exists:kelas,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'semester_id' => 'required|exists:semesters,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruang_id' => 'required|exists:ruang,id',
        ];

        $validated = $request->validate($rules);

        // Check ruang conflict (exclude current jadwal)
        $ruangConflict = JadwalKuliah::checkRuangConflict(
            $validated['ruang_id'],
            $validated['hari'],
            $validated['jam_mulai'],
            $validated['jam_selesai'],
            $jadwalKuliah->id
        );

        if ($ruangConflict) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ruangan bentrok dengan jadwal: ' . 
                    $ruangConflict->kelas->mataKuliah->nama_mk . 
                    ' (' . $ruangConflict->kelas->dosen->nama_dosen . ') pada ' . 
                    $ruangConflict->hari . ' jam ' . $ruangConflict->waktu);
        }

        // Check dosen conflict (exclude current jadwal)
        $dosenConflict = JadwalKuliah::checkDosenConflict(
            $validated['kelas_id'],
            $validated['hari'],
            $validated['jam_mulai'],
            $validated['jam_selesai'],
            $jadwalKuliah->id
        );

        if ($dosenConflict) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Dosen bentrok dengan jadwal: ' . 
                    $dosenConflict->kelas->mataKuliah->nama_mk . 
                    ' pada ' . $dosenConflict->hari . ' jam ' . $dosenConflict->waktu);
        }

        // Check if ruang can be used by this kelas
        $kelas = Kelas::with('mataKuliah.programStudi')->find($validated['kelas_id']);
        $ruang = Ruang::find($validated['ruang_id']);
        
        if ($kelas && $kelas->mataKuliah && $kelas->mataKuliah->programStudi) {
            $canUse = $ruang->canBeUsedBy(
                $kelas->mataKuliah->program_studi_id,
                $kelas->mataKuliah->programStudi->fakultas_id
            );
            
            if (!$canUse) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Ruangan ini tidak dapat digunakan oleh program studi ' . 
                        $kelas->mataKuliah->programStudi->nama_prodi);
            }
        }

        DB::beginTransaction();
        try {
            $jadwalKuliah->update($validated);

            DB::commit();
            return redirect()->route('jadwal-kuliah.index')
                ->with('success', 'Jadwal kuliah berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JadwalKuliah $jadwalKuliah)
    {
        $jadwalKuliah->deleted_by = Auth::id();
        $jadwalKuliah->save();
        $jadwalKuliah->delete();

        return redirect()->route('jadwal-kuliah.index')
            ->with('success', 'Jadwal kuliah berhasil dihapus');
    }

    /**
     * Trash - soft deleted items
     */
    public function trash()
    {
        $jadwal = JadwalKuliah::onlyTrashed()
            ->with([
                'kelas.mataKuliah',
                'kelas.dosen',
                'ruang',
                'deletedBy'
            ])
            ->latest('deleted_at')
            ->paginate(20);

        return view('jadwal-kuliah.trash', compact('jadwal'));
    }

    /**
     * Restore dari trash
     */
    public function restore($id)
    {
        $jadwal = JadwalKuliah::onlyTrashed()->findOrFail($id);
        $jadwal->restore();

        return redirect()->route('jadwal-kuliah.trash')
            ->with('success', 'Jadwal kuliah berhasil dipulihkan');
    }

    /**
     * Force delete permanent
     */
    public function forceDelete($id)
    {
        $jadwal = JadwalKuliah::onlyTrashed()->findOrFail($id);
        $jadwal->forceDelete();

        return redirect()->route('jadwal-kuliah.trash')
            ->with('success', 'Jadwal kuliah berhasil dihapus permanen');
    }

    /**
     * Get available ruang for specific time slot
     */
    public function getAvailableRuang(Request $request)
    {
        $hari = $request->hari;
        $jamMulai = $request->jam_mulai;
        $jamSelesai = $request->jam_selesai;
        $kelasId = $request->kelas_id;
        $excludeId = $request->exclude_id;

        // Get kelas to determine prodi/fakultas
        $kelas = Kelas::with('mataKuliah.programStudi')->find($kelasId);
        
        if (!$kelas) {
            return response()->json([]);
        }

        // Get ruang that can be used by this prodi
        $ruangQuery = Ruang::where('status', 'Aktif');
        
        if ($kelas->mataKuliah && $kelas->mataKuliah->programStudi) {
            $ruangQuery->where(function($q) use ($kelas) {
                $q->where('tingkat_kepemilikan', 'Universitas')
                  ->orWhere(function($q2) use ($kelas) {
                      $q2->where('tingkat_kepemilikan', 'Fakultas')
                         ->where('fakultas_id', $kelas->mataKuliah->programStudi->fakultas_id);
                  })
                  ->orWhere(function($q3) use ($kelas) {
                      $q3->where('tingkat_kepemilikan', 'Prodi')
                         ->where('program_studi_id', $kelas->mataKuliah->program_studi_id);
                  });
            });
        }

        $availableRuang = $ruangQuery->get()->filter(function($ruang) use ($hari, $jamMulai, $jamSelesai, $excludeId) {
            $conflict = JadwalKuliah::checkRuangConflict($ruang->id, $hari, $jamMulai, $jamSelesai, $excludeId);
            return !$conflict;
        });

        return response()->json($availableRuang->values());
    }
}
