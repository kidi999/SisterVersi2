<?php

namespace App\Http\Controllers;

use App\Models\TagihanMahasiswa;
use App\Models\Mahasiswa;
use App\Models\JenisPembayaran;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\TahunAkademik;
use App\Models\Semester;
use App\Models\FileUpload;
use App\Support\TabularExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TagihanMahasiswaController extends Controller
{
    private function filteredQuery(Request $request)
    {
        $query = TagihanMahasiswa::with([
            'mahasiswa.programStudi.fakultas',
            'jenisPembayaran',
            'tahunAkademik',
            'semester'
        ]);

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('mahasiswa', function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                    ->orWhere('nama_mahasiswa', 'like', "%{$search}%");
            })->orWhere('nomor_tagihan', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by jenis pembayaran
        if ($request->filled('jenis_pembayaran_id')) {
            $query->where('jenis_pembayaran_id', $request->jenis_pembayaran_id);
        }

        // Filter by tahun akademik
        if ($request->filled('tahun_akademik_id')) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }

        // Filter by semester
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        // Filter by user role
        $user = Auth::user();
        if ($user->role->name === 'admin_fakultas') {
            $query->whereHas('mahasiswa.programStudi', function ($q) use ($user) {
                $q->where('fakultas_id', $user->fakultas_id);
            });
        } elseif ($user->role->name === 'admin_prodi') {
            $query->whereHas('mahasiswa', function ($q) use ($user) {
                $q->where('program_studi_id', $user->program_studi_id);
            });
        }

        return $query;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tagihan = $this->filteredQuery($request)
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $jenisPembayaran = JenisPembayaran::active()->orderBy('urutan')->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun_mulai', 'desc')->get();
        $semesters = Semester::orderBy('created_at', 'desc')->get();

        return view('tagihan-mahasiswa.index', compact('tagihan', 'jenisPembayaran', 'tahunAkademik', 'semesters'));
    }

    public function exportExcel(Request $request)
    {
        $items = $this->filteredQuery($request)->orderBy('created_at', 'desc')->get();

        $headers = [
            'No. Tagihan',
            'NIM',
            'Nama Mahasiswa',
            'Program Studi',
            'Fakultas',
            'Jenis Pembayaran',
            'Tahun Akademik',
            'Semester',
            'Tanggal Tagihan',
            'Jatuh Tempo',
            'Jumlah Tagihan',
            'Jumlah Dibayar',
            'Sisa Tagihan',
            'Status',
        ];

        $rows = $items->map(function ($t) {
            return [
                $t->nomor_tagihan,
                $t->mahasiswa->nim ?? '-',
                $t->mahasiswa->nama_mahasiswa ?? '-',
                $t->mahasiswa->programStudi->nama_prodi ?? '-',
                $t->mahasiswa->programStudi->fakultas->nama_fakultas ?? '-',
                $t->jenisPembayaran->nama ?? '-',
                $t->tahunAkademik->nama ?? '-',
                $t->semester->nama ?? '-',
                optional($t->tanggal_tagihan)->format('d/m/Y'),
                optional($t->tanggal_jatuh_tempo)->format('d/m/Y'),
                $t->jumlah_tagihan,
                $t->jumlah_dibayar,
                $t->sisa_tagihan,
                $t->status,
            ];
        });

        $html = TabularExport::htmlTable($headers, $rows);
        return TabularExport::excelResponse('tagihan_mahasiswa.xls', $html);
    }

    public function exportPdf(Request $request)
    {
        $items = $this->filteredQuery($request)->orderBy('created_at', 'desc')->get();

        $headers = [
            'No. Tagihan',
            'NIM',
            'Nama Mahasiswa',
            'Program Studi',
            'Fakultas',
            'Jenis Pembayaran',
            'Tahun Akademik',
            'Semester',
            'Tanggal Tagihan',
            'Jatuh Tempo',
            'Jumlah Tagihan',
            'Jumlah Dibayar',
            'Sisa Tagihan',
            'Status',
        ];

        $rows = $items->map(function ($t) {
            return [
                $t->nomor_tagihan,
                $t->mahasiswa->nim ?? '-',
                $t->mahasiswa->nama_mahasiswa ?? '-',
                $t->mahasiswa->programStudi->nama_prodi ?? '-',
                $t->mahasiswa->programStudi->fakultas->nama_fakultas ?? '-',
                $t->jenisPembayaran->nama ?? '-',
                $t->tahunAkademik->nama ?? '-',
                $t->semester->nama ?? '-',
                optional($t->tanggal_tagihan)->format('d/m/Y'),
                optional($t->tanggal_jatuh_tempo)->format('d/m/Y'),
                $t->jumlah_tagihan,
                $t->jumlah_dibayar,
                $t->sisa_tagihan,
                $t->status,
            ];
        });

        $html = TabularExport::htmlTable($headers, $rows);
        return Pdf::loadHTML($html)->download('tagihan_mahasiswa.pdf');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get mahasiswa based on user role
        $mahasiswaQuery = Mahasiswa::with('programStudi.fakultas')
                                    ->where('status', 'Aktif');
        
        if ($user->role->name === 'admin_fakultas') {
            $mahasiswaQuery->whereHas('programStudi', function($q) use ($user) {
                $q->where('fakultas_id', $user->fakultas_id);
            });
        } elseif ($user->role->name === 'admin_prodi') {
            $mahasiswaQuery->where('program_studi_id', $user->program_studi_id);
        }
        
        $mahasiswa = $mahasiswaQuery->orderBy('nama_mahasiswa')->get();
        $jenisPembayaran = JenisPembayaran::active()->orderBy('urutan')->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun_mulai', 'desc')->get();
        $semesters = Semester::orderBy('created_at', 'desc')->get();

        return view('tagihan-mahasiswa.create', compact('mahasiswa', 'jenisPembayaran', 'tahunAkademik', 'semesters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
            'jenis_pembayaran_id' => 'required|exists:jenis_pembayaran,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'semester_id' => 'required|exists:semesters,id',
            'jumlah_tagihan' => 'required|numeric|min:0',
            'tanggal_tagihan' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_tagihan',
            'denda' => 'nullable|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'file_ids' => 'nullable|array',
            'file_ids.*' => 'exists:file_uploads,id',
        ]);

        // Check duplicate tagihan
        $exists = TagihanMahasiswa::where('mahasiswa_id', $validated['mahasiswa_id'])
                                  ->where('jenis_pembayaran_id', $validated['jenis_pembayaran_id'])
                                  ->where('tahun_akademik_id', $validated['tahun_akademik_id'])
                                  ->where('semester_id', $validated['semester_id'])
                                  ->exists();

        if ($exists) {
            return back()->with('error', 'Tagihan untuk mahasiswa ini pada jenis pembayaran, tahun akademik, dan semester yang sama sudah ada.')
                        ->withInput();
        }

        DB::beginTransaction();
        try {
            $validated['nomor_tagihan'] = TagihanMahasiswa::generateNomorTagihan();
            $validated['jumlah_dibayar'] = 0;
            
            $jumlahTagihan = $validated['jumlah_tagihan'];
            $denda = $validated['denda'] ?? 0;
            $diskon = $validated['diskon'] ?? 0;
            $validated['sisa_tagihan'] = $jumlahTagihan + $denda - $diskon;
            
            $validated['status'] = 'Belum Dibayar';
            $validated['created_by'] = Auth::user()->name;

            $tagihan = TagihanMahasiswa::create($validated);

            // Attach uploaded files (draft attachments)
            if ($request->filled('file_ids') && is_array($request->file_ids)) {
                FileUpload::whereIn('id', $request->file_ids)
                    ->update([
                        'fileable_type' => TagihanMahasiswa::class,
                        'fileable_id' => $tagihan->id,
                    ]);
            }

            DB::commit();
            return redirect()->route('tagihan-mahasiswa.index')
                           ->with('success', 'Tagihan berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal membuat tagihan: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TagihanMahasiswa $tagihanMahasiswa)
    {
        $tagihanMahasiswa->load([
            'mahasiswa.programStudi.fakultas',
            'jenisPembayaran',
            'tahunAkademik',
            'semester',
            'pembayaran.verifiedBy',
            'files',
        ]);

        return view('tagihan-mahasiswa.show', compact('tagihanMahasiswa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TagihanMahasiswa $tagihanMahasiswa)
    {
        $tagihanMahasiswa->load('mahasiswa', 'jenisPembayaran', 'tahunAkademik', 'semester', 'files');
        
        $jenisPembayaran = JenisPembayaran::active()->orderBy('urutan')->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun_mulai', 'desc')->get();
        $semesters = Semester::orderBy('created_at', 'desc')->get();

        return view('tagihan-mahasiswa.edit', compact('tagihanMahasiswa', 'jenisPembayaran', 'tahunAkademik', 'semesters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TagihanMahasiswa $tagihanMahasiswa)
    {
        $validated = $request->validate([
            'jumlah_tagihan' => 'required|numeric|min:0',
            'tanggal_tagihan' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_tagihan',
            'denda' => 'nullable|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'file_ids' => 'nullable|array',
            'file_ids.*' => 'exists:file_uploads,id',
        ]);

        DB::beginTransaction();
        try {
            $jumlahTagihan = $validated['jumlah_tagihan'];
            $denda = $validated['denda'] ?? 0;
            $diskon = $validated['diskon'] ?? 0;
            $validated['sisa_tagihan'] = $jumlahTagihan + $denda - $diskon - $tagihanMahasiswa->jumlah_dibayar;
            
            $validated['updated_by'] = Auth::user()->name;

            $tagihanMahasiswa->update($validated);
            $tagihanMahasiswa->updateStatus();

            if ($request->has('file_ids') && is_array($request->file_ids)) {
                FileUpload::whereIn('id', $request->file_ids)
                    ->update([
                        'fileable_type' => TagihanMahasiswa::class,
                        'fileable_id' => $tagihanMahasiswa->id,
                    ]);
            }

            DB::commit();
            return redirect()->route('tagihan-mahasiswa.index')
                           ->with('success', 'Tagihan berhasil diupdate.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal mengupdate tagihan: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TagihanMahasiswa $tagihanMahasiswa)
    {
        if ($tagihanMahasiswa->jumlah_dibayar > 0) {
            return back()->with('error', 'Tidak dapat menghapus tagihan yang sudah ada pembayaran.');
        }

        DB::beginTransaction();
        try {
            $tagihanMahasiswa->deleted_by = Auth::user()->name;
            $tagihanMahasiswa->save();
            $tagihanMahasiswa->delete();

            DB::commit();
            return redirect()->route('tagihan-mahasiswa.index')
                           ->with('success', 'Tagihan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus tagihan: ' . $e->getMessage());
        }
    }

    /**
     * Batch create tagihan for multiple mahasiswa
     */
    public function batchCreate()
    {
        $user = Auth::user();
        
        $jenisPembayaran = JenisPembayaran::active()->orderBy('urutan')->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun_mulai', 'desc')->get();
        $semesters = Semester::orderBy('created_at', 'desc')->get();

        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        $programStudi = ProgramStudi::with('fakultas')->orderBy('nama_prodi')->get();

        return view('tagihan-mahasiswa.batch-create', compact('jenisPembayaran', 'tahunAkademik', 'semesters', 'fakultas', 'programStudi'));
    }

    /**
     * Store batch tagihan
     */
    public function batchStore(Request $request)
    {
        $validated = $request->validate([
            'jenis_pembayaran_id' => 'required|exists:jenis_pembayaran,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'semester_id' => 'required|exists:semesters,id',
            'jumlah_tagihan' => 'required|numeric|min:0',
            'tanggal_tagihan' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_tagihan',
            'filter_type' => 'required|in:all,fakultas,prodi,semester_mahasiswa',
            'fakultas_id' => 'required_if:filter_type,fakultas,prodi|nullable|exists:fakultas,id',
            'program_studi_id' => 'required_if:filter_type,prodi|nullable|exists:program_studi,id',
            'semester_mahasiswa' => 'required_if:filter_type,semester_mahasiswa|nullable|integer|min:1',
        ]);

        $user = Auth::user();
        
        // Build mahasiswa query based on filter
        $mahasiswaQuery = Mahasiswa::where('status', 'Aktif');

        if ($validated['filter_type'] === 'fakultas' && $validated['fakultas_id']) {
            $mahasiswaQuery->whereHas('programStudi', function($q) use ($validated) {
                $q->where('fakultas_id', $validated['fakultas_id']);
            });
        } elseif ($validated['filter_type'] === 'prodi' && $validated['program_studi_id']) {
            $mahasiswaQuery->where('program_studi_id', $validated['program_studi_id']);
        } elseif ($validated['filter_type'] === 'semester_mahasiswa' && $validated['semester_mahasiswa']) {
            $mahasiswaQuery->where('semester', $validated['semester_mahasiswa']);
        }

        // Apply role-based filtering
        if ($user->role->name === 'admin_fakultas') {
            $mahasiswaQuery->whereHas('programStudi', function($q) use ($user) {
                $q->where('fakultas_id', $user->fakultas_id);
            });
        } elseif ($user->role->name === 'admin_prodi') {
            $mahasiswaQuery->where('program_studi_id', $user->program_studi_id);
        }

        $mahasiswaList = $mahasiswaQuery->get();

        if ($mahasiswaList->isEmpty()) {
            return back()->with('error', 'Tidak ada mahasiswa yang sesuai dengan filter.')
                        ->withInput();
        }

        DB::beginTransaction();
        try {
            $created = 0;
            $skipped = 0;

            foreach ($mahasiswaList as $mahasiswa) {
                // Check if tagihan already exists
                $exists = TagihanMahasiswa::where('mahasiswa_id', $mahasiswa->id)
                                          ->where('jenis_pembayaran_id', $validated['jenis_pembayaran_id'])
                                          ->where('tahun_akademik_id', $validated['tahun_akademik_id'])
                                          ->where('semester_id', $validated['semester_id'])
                                          ->exists();

                if (!$exists) {
                    TagihanMahasiswa::create([
                        'mahasiswa_id' => $mahasiswa->id,
                        'jenis_pembayaran_id' => $validated['jenis_pembayaran_id'],
                        'tahun_akademik_id' => $validated['tahun_akademik_id'],
                        'semester_id' => $validated['semester_id'],
                        'nomor_tagihan' => TagihanMahasiswa::generateNomorTagihan(),
                        'jumlah_tagihan' => $validated['jumlah_tagihan'],
                        'jumlah_dibayar' => 0,
                        'sisa_tagihan' => $validated['jumlah_tagihan'],
                        'tanggal_tagihan' => $validated['tanggal_tagihan'],
                        'tanggal_jatuh_tempo' => $validated['tanggal_jatuh_tempo'],
                        'status' => 'Belum Dibayar',
                        'denda' => 0,
                        'diskon' => 0,
                        'created_by' => Auth::user()->name,
                    ]);
                    $created++;
                } else {
                    $skipped++;
                }
            }

            DB::commit();
            
            $message = "Berhasil membuat {$created} tagihan.";
            if ($skipped > 0) {
                $message .= " {$skipped} tagihan dilewati karena sudah ada.";
            }

            return redirect()->route('tagihan-mahasiswa.index')
                           ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal membuat tagihan: ' . $e->getMessage())
                        ->withInput();
        }
    }
}
