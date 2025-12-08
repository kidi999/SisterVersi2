<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use App\Models\Krs;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NilaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Nilai::with(['krs.mahasiswa.programStudi', 'krs.kelas.mataKuliah', 'krs.kelas.dosen']);

        // Filter berdasarkan role
        if ($user->role->name == 'mahasiswa') {
            // Mahasiswa hanya lihat nilai sendiri
            if ($user->mahasiswa_id) {
                $query->whereHas('krs', function($q) use ($user) {
                    $q->where('mahasiswa_id', $user->mahasiswa_id);
                });
            } else {
                // Jika tidak ada mahasiswa_id, redirect dengan error
                return redirect()->route('dashboard')
                    ->with('error', 'Data mahasiswa tidak ditemukan. Silakan hubungi administrator.');
            }
        } elseif ($user->role->name == 'dosen') {
            // Dosen lihat nilai mahasiswa di kelas yang dia ampu
            $query->whereHas('krs.kelas', function($q) use ($user) {
                $q->where('dosen_id', $user->dosen->id ?? 0);
            });
        } elseif ($user->role->name == 'admin_prodi') {
            // Admin prodi lihat nilai mahasiswa di program studinya
            $query->whereHas('krs.mahasiswa', function($q) use ($user) {
                $q->where('program_studi_id', $user->program_studi_id);
            });
        } elseif ($user->role->name == 'admin_fakultas') {
            // Admin fakultas lihat nilai mahasiswa di fakultasnya
            $query->whereHas('krs.mahasiswa.programStudi', function($q) use ($user) {
                $q->where('fakultas_id', $user->fakultas_id);
            });
        }
        // super_admin dan admin_universitas lihat semua

        // Filter by search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('krs.mahasiswa', function($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhere('nama_mahasiswa', 'like', "%{$search}%");
            });
        }

        // Filter by tahun ajaran
        if ($request->has('tahun_ajaran') && $request->tahun_ajaran != '') {
            $query->whereHas('krs', function($q) use ($request) {
                $q->where('tahun_ajaran', $request->tahun_ajaran);
            });
        }

        // Filter by semester
        if ($request->has('semester') && $request->semester != '') {
            $query->whereHas('krs', function($q) use ($request) {
                $q->where('semester', $request->semester);
            });
        }

        $nilai = $query->latest()->paginate(10)->withQueryString();
        
        // Get distinct tahun ajaran for filter
        $tahunAjaranList = Krs::select('tahun_ajaran')->distinct()->orderBy('tahun_ajaran', 'desc')->pluck('tahun_ajaran');

        return view('nilai.index', compact('nilai', 'tahunAjaranList'));
    }

    /**
     * Show the form for creating a new resource (Dosen input nilai).
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        // Only dosen and admin can create nilai
        if (!in_array($user->role->name, ['dosen', 'admin_prodi', 'admin_fakultas', 'admin_universitas', 'super_admin'])) {
            return redirect()->route('nilai.index')
                           ->with('error', 'Anda tidak memiliki akses untuk menginput nilai');
        }

        // Get KRS yang sudah disetujui tapi belum ada nilainya
        $krsQuery = Krs::with(['mahasiswa', 'kelas.mataKuliah'])
            ->where('status', 'Disetujui')
            ->whereDoesntHave('nilai');

        // Filter untuk dosen: hanya kelas yang dia ampu
        if ($user->role->name == 'dosen') {
            $krsQuery->whereHas('kelas', function($q) use ($user) {
                $q->where('dosen_id', $user->dosen->id ?? 0);
            });
        }

        // Filter by kelas_id if provided
        if ($request->has('kelas_id') && $request->kelas_id != '') {
            $krsQuery->where('kelas_id', $request->kelas_id);
        }

        $krsList = $krsQuery->get();

        // Get kelas list for filter (dosen's classes only)
        $kelasList = \App\Models\Kelas::with('mataKuliah')
            ->when($user->role->name == 'dosen', function($q) use ($user) {
                $q->where('dosen_id', $user->dosen->id ?? 0);
            })
            ->get();

        return view('nilai.create', compact('krsList', 'kelasList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'krs_id' => 'required|exists:krs,id',
            'nilai_tugas' => 'required|numeric|min:0|max:100',
            'nilai_uts' => 'required|numeric|min:0|max:100',
            'nilai_uas' => 'required|numeric|min:0|max:100',
        ]);

        try {
            // Check if nilai already exists
            $exists = Nilai::where('krs_id', $validated['krs_id'])->exists();
            if ($exists) {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Nilai untuk KRS ini sudah ada');
            }

            // Create nilai and auto-calculate
            $nilai = new Nilai($validated);
            $nilai->hitungNilaiAkhir();
            $nilai->created_by = Auth::user()->name;
            $nilai->created_at = now();
            $nilai->save();

            return redirect()->route('nilai.index')
                           ->with('success', 'Nilai berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Nilai $nilai)
    {
        $nilai->load(['krs.mahasiswa.programStudi', 'krs.kelas.mataKuliah', 'krs.kelas.dosen']);
        
        return view('nilai.show', compact('nilai'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Nilai $nilai)
    {
        $nilai->load(['krs.mahasiswa', 'krs.kelas.mataKuliah']);
        
        return view('nilai.edit', compact('nilai'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Nilai $nilai)
    {
        $validated = $request->validate([
            'nilai_tugas' => 'required|numeric|min:0|max:100',
            'nilai_uts' => 'required|numeric|min:0|max:100',
            'nilai_uas' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $nilai->fill($validated);
            $nilai->hitungNilaiAkhir();
            $nilai->updated_by = Auth::user()->name;
            $nilai->save();

            return redirect()->route('nilai.index')
                           ->with('success', 'Nilai berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal memperbarui nilai: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nilai $nilai)
    {
        try {
            $nilai->update([
                'deleted_by' => Auth::user()->name,
            ]);
            
            $nilai->delete();

            return redirect()->route('nilai.index')
                           ->with('success', 'Nilai berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menghapus nilai: ' . $e->getMessage());
        }
    }

    /**
     * Display KHS (Kartu Hasil Studi) - Semester grades report.
     */
    public function khs($mahasiswaId, $tahunAjaran, $semester)
    {
        $mahasiswa = Mahasiswa::with('programStudi.fakultas')->findOrFail($mahasiswaId);
        
        // Get nilai for specific semester
        $nilaiList = Nilai::with(['krs.kelas.mataKuliah'])
            ->whereHas('krs', function($q) use ($mahasiswaId, $tahunAjaran, $semester) {
                $q->where('mahasiswa_id', $mahasiswaId)
                  ->where('tahun_ajaran', $tahunAjaran)
                  ->where('semester', $semester)
                  ->where('status', 'Disetujui');
            })
            ->get();

        // Calculate IPS (Indeks Prestasi Semester)
        $totalSks = 0;
        $totalBobotSks = 0;

        foreach ($nilaiList as $nilai) {
            $sks = $nilai->krs->kelas->mataKuliah->sks;
            $totalSks += $sks;
            $totalBobotSks += ($nilai->bobot * $sks);
        }

        $ips = $totalSks > 0 ? round($totalBobotSks / $totalSks, 2) : 0;

        return view('nilai.khs', compact('mahasiswa', 'nilaiList', 'tahunAjaran', 'semester', 'totalSks', 'ips'));
    }

    /**
     * Display Transkrip - Full academic transcript.
     */
    public function transkrip($mahasiswaId)
    {
        $mahasiswa = Mahasiswa::with('programStudi.fakultas')->findOrFail($mahasiswaId);
        
        // Get all nilai grouped by semester
        $nilaiList = Nilai::with(['krs.kelas.mataKuliah'])
            ->whereHas('krs', function($q) use ($mahasiswaId) {
                $q->where('mahasiswa_id', $mahasiswaId)
                  ->where('status', 'Disetujui');
            })
            ->get();

        // Group by tahun_ajaran and semester
        $nilaiGrouped = $nilaiList->groupBy(function($item) {
            return $item->krs->tahun_ajaran . '-' . $item->krs->semester;
        });

        // Calculate IPK (Indeks Prestasi Kumulatif)
        $totalSksKumulatif = 0;
        $totalBobotSksKumulatif = 0;

        foreach ($nilaiList as $nilai) {
            $sks = $nilai->krs->kelas->mataKuliah->sks;
            $totalSksKumulatif += $sks;
            $totalBobotSksKumulatif += ($nilai->bobot * $sks);
        }

        $ipk = $totalSksKumulatif > 0 ? round($totalBobotSksKumulatif / $totalSksKumulatif, 2) : 0;

        return view('nilai.transkrip', compact('mahasiswa', 'nilaiGrouped', 'totalSksKumulatif', 'ipk'));
    }

    /**
     * Input nilai batch per kelas.
     */
    public function batch($kelasId)
    {
        $kelas = \App\Models\Kelas::with('mataKuliah', 'dosen')->findOrFail($kelasId);
        
        // Get all approved KRS for this kelas
        $krsList = Krs::with(['mahasiswa', 'nilai'])
            ->where('kelas_id', $kelasId)
            ->where('status', 'Disetujui')
            ->get();

        return view('nilai.batch', compact('kelas', 'krsList'));
    }

    /**
     * Store batch nilai.
     */
    public function storeBatch(Request $request, $kelasId)
    {
        $validated = $request->validate([
            'nilai' => 'required|array',
            'nilai.*.krs_id' => 'required|exists:krs,id',
            'nilai.*.nilai_tugas' => 'required|numeric|min:0|max:100',
            'nilai.*.nilai_uts' => 'required|numeric|min:0|max:100',
            'nilai.*.nilai_uas' => 'required|numeric|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['nilai'] as $nilaiData) {
                // Check if nilai already exists
                $nilai = Nilai::where('krs_id', $nilaiData['krs_id'])->first();
                
                if ($nilai) {
                    // Update existing
                    $nilai->fill($nilaiData);
                    $nilai->hitungNilaiAkhir();
                    $nilai->updated_by = Auth::user()->name;
                    $nilai->save();
                } else {
                    // Create new
                    $nilai = new Nilai($nilaiData);
                    $nilai->hitungNilaiAkhir();
                    $nilai->created_by = Auth::user()->name;
                    $nilai->created_at = now();
                    $nilai->save();
                }
            }

            DB::commit();

            return redirect()->route('nilai.index')
                           ->with('success', 'Nilai batch berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menyimpan nilai batch: ' . $e->getMessage());
        }
    }
}
