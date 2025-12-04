<?php

namespace App\Http\Controllers;

use App\Models\Krs;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KrsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Krs::with(['mahasiswa.programStudi', 'kelas.mataKuliah']);

        // Filter berdasarkan role
        if ($user->role->name == 'mahasiswa') {
            // Mahasiswa hanya lihat KRS sendiri
            if ($user->mahasiswa_id) {
                $query->where('mahasiswa_id', $user->mahasiswa_id);
            } else {
                // Jika tidak ada mahasiswa_id, redirect dengan error
                return redirect()->route('dashboard')
                    ->with('error', 'Data mahasiswa tidak ditemukan. Silakan hubungi administrator.');
            }
        } elseif ($user->role->name == 'dosen') {
            // Dosen lihat KRS mahasiswa yang dia ampu kelasnya
            $query->whereHas('kelas', function($q) use ($user) {
                $q->where('dosen_id', $user->dosen->id ?? 0);
            });
        } elseif (in_array($user->role->name, ['admin_prodi'])) {
            // Admin prodi lihat KRS mahasiswa di program studinya
            $query->whereHas('mahasiswa', function($q) use ($user) {
                $q->where('program_studi_id', $user->program_studi_id);
            });
        } elseif ($user->role->name == 'admin_fakultas') {
            // Admin fakultas lihat KRS mahasiswa di fakultasnya
            $query->whereHas('mahasiswa.programStudi', function($q) use ($user) {
                $q->where('fakultas_id', $user->fakultas_id);
            });
        }
        // super_admin dan admin_universitas lihat semua

        // Filter by search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('mahasiswa', function($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhere('nama_mahasiswa', 'like', "%{$search}%");
            });
        }

        // Filter by tahun ajaran
        if ($request->has('tahun_ajaran') && $request->tahun_ajaran != '') {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }

        // Filter by semester
        if ($request->has('semester') && $request->semester != '') {
            $query->where('semester', $request->semester);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $krs = $query->latest()->paginate(10)->withQueryString();
        
        // Get distinct tahun ajaran for filter
        $tahunAjaranList = Krs::select('tahun_ajaran')->distinct()->orderBy('tahun_ajaran', 'desc')->pluck('tahun_ajaran');

        return view('krs.index', compact('krs', 'tahunAjaranList'));
    }

    /**
     * Show the form for creating a new resource (Mahasiswa mengisi KRS).
     */
    public function create()
    {
        $user = Auth::user();
        
        // Only mahasiswa can create KRS
        if ($user->role->name != 'mahasiswa') {
            return redirect()->route('krs.index')
                           ->with('error', 'Hanya mahasiswa yang dapat mengisi KRS');
        }

        if (!$user->mahasiswa_id) {
            return redirect()->route('dashboard')
                           ->with('error', 'Data mahasiswa tidak ditemukan');
        }
        
        $mahasiswa = $user->mahasiswa;

        // Get active tahun akademik
        $tahunAkademik = TahunAkademik::where('is_active', true)->first();
        
        // Get available kelas for this mahasiswa's program studi
        $kelasList = Kelas::with(['mataKuliah', 'dosen'])
            ->whereHas('mataKuliah', function($q) use ($mahasiswa) {
                $q->where('program_studi_id', $mahasiswa->program_studi_id);
            })
            ->where('tahun_ajaran', $tahunAkademik->tahun_ajaran ?? date('Y'))
            ->whereColumn('terisi', '<', 'kapasitas')
            ->get();

        // Get already enrolled kelas untuk mahasiswa ini
        $enrolledKelasIds = Krs::where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_ajaran', $tahunAkademik->tahun_ajaran ?? date('Y'))
            ->where('status', '!=', 'Ditolak')
            ->pluck('kelas_id')
            ->toArray();

        return view('krs.create', compact('mahasiswa', 'tahunAkademik', 'kelasList', 'enrolledKelasIds'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_ids' => 'required|array',
            'kelas_ids.*' => 'exists:kelas,id',
        ]);

        $user = Auth::user();
        
        if (!$user->mahasiswa_id) {
            return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan');
        }
        
        $mahasiswa = $user->mahasiswa;

        $tahunAkademik = TahunAkademik::where('is_active', true)->first();

        try {
            DB::beginTransaction();

            $totalSks = 0;
            $errors = [];

            foreach ($validated['kelas_ids'] as $kelasId) {
                $kelas = Kelas::with('mataKuliah')->findOrFail($kelasId);

                // Check kapasitas
                if ($kelas->terisi >= $kelas->kapasitas) {
                    $errors[] = "Kelas {$kelas->nama_kelas} sudah penuh";
                    continue;
                }

                // Check duplicate
                $exists = Krs::where('mahasiswa_id', $mahasiswa->id)
                    ->where('kelas_id', $kelasId)
                    ->where('tahun_ajaran', $tahunAkademik->tahun_ajaran)
                    ->where('status', '!=', 'Ditolak')
                    ->exists();

                if ($exists) {
                    $errors[] = "Anda sudah terdaftar di kelas {$kelas->nama_kelas}";
                    continue;
                }

                // Create KRS
                Krs::create([
                    'mahasiswa_id' => $mahasiswa->id,
                    'kelas_id' => $kelasId,
                    'tahun_ajaran' => $tahunAkademik->tahun_ajaran,
                    'semester' => $tahunAkademik->semester,
                    'status' => 'Menunggu',
                    'tanggal_pengajuan' => now(),
                    'inserted_by' => $user->name,
                    'inserted_at' => now(),
                ]);

                // Update terisi di kelas
                $kelas->increment('terisi');

                $totalSks += $kelas->mataKuliah->sks;
            }

            DB::commit();

            if (count($errors) > 0) {
                return redirect()->route('krs.index')
                               ->with('warning', 'KRS berhasil disimpan dengan catatan: ' . implode(', ', $errors));
            }

            return redirect()->route('krs.index')
                           ->with('success', "KRS berhasil disimpan. Total SKS: {$totalSks}");
                           
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menyimpan KRS: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Krs $kr)
    {
        $kr->load(['mahasiswa.programStudi', 'kelas.mataKuliah', 'kelas.dosen', 'nilai']);
        
        return view('krs.show', compact('kr'));
    }

    /**
     * Approve KRS (Admin/Dosen Wali).
     */
    public function approve($id)
    {
        try {
            $krs = Krs::findOrFail($id);
            
            $krs->update([
                'status' => 'Disetujui',
                'tanggal_persetujuan' => now(),
                'updated_by' => Auth::user()->name,
            ]);

            return redirect()->back()
                           ->with('success', 'KRS berhasil disetujui');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menyetujui KRS: ' . $e->getMessage());
        }
    }

    /**
     * Reject KRS (Admin/Dosen Wali).
     */
    public function reject($id)
    {
        try {
            $krs = Krs::findOrFail($id);
            
            // Decrease terisi count in kelas
            $krs->kelas->decrement('terisi');
            
            $krs->update([
                'status' => 'Ditolak',
                'updated_by' => Auth::user()->name,
            ]);

            return redirect()->back()
                           ->with('success', 'KRS berhasil ditolak');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menolak KRS: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage (Cancel KRS by Mahasiswa).
     */
    public function destroy(Krs $kr)
    {
        try {
            // Only allow cancel if status is still 'Menunggu'
            if ($kr->status != 'Menunggu') {
                return redirect()->back()
                               ->with('error', 'KRS yang sudah disetujui/ditolak tidak dapat dibatalkan');
            }

            // Decrease terisi count in kelas
            $kr->kelas->decrement('terisi');

            $kr->update([
                'deleted_by' => Auth::user()->name,
            ]);
            
            $kr->delete();

            return redirect()->route('krs.index')
                           ->with('success', 'KRS berhasil dibatalkan');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal membatalkan KRS: ' . $e->getMessage());
        }
    }

    /**
     * Print KRS (Kartu Rencana Studi).
     */
    public function print($mahasiswaId, $tahunAjaran, $semester)
    {
        $mahasiswa = Mahasiswa::with('programStudi.fakultas')->findOrFail($mahasiswaId);
        
        $krsItems = Krs::with(['kelas.mataKuliah', 'kelas.dosen'])
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->where('status', 'Disetujui')
            ->get();

        $totalSks = $krsItems->sum(function($item) {
            return $item->kelas->mataKuliah->sks;
        });

        return view('krs.print', compact('mahasiswa', 'krsItems', 'totalSks', 'tahunAjaran', 'semester'));
    }
}
