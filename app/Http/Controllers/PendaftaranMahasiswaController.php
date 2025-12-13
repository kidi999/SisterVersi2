<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranMahasiswa;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\Fakultas;
use App\Models\Province;
use App\Models\FileUpload;
use App\Support\TabularExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PendaftaranMahasiswaController extends Controller
{
    private function filteredQuery(Request $request)
    {
        $query = PendaftaranMahasiswa::with(['programStudi.fakultas', 'village']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan jalur masuk
        if ($request->filled('jalur_masuk')) {
            $query->where('jalur_masuk', $request->jalur_masuk);
        }

        // Filter berdasarkan tahun akademik
        if ($request->filled('tahun_akademik')) {
            $query->where('tahun_akademik', $request->tahun_akademik);
        }

        // Filter berdasarkan program studi
        if ($request->filled('program_studi_id')) {
            $query->where('program_studi_id', $request->program_studi_id);
        }

        // Filter berdasarkan fakultas
        if ($request->filled('fakultas_id')) {
            $query->whereHas('programStudi', function ($q) use ($request) {
                $q->where('fakultas_id', $request->fakultas_id);
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_pendaftaran', 'like', "%{$search}%")
                    ->orWhere('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pendaftaran = $this->filteredQuery($request)
            ->latest('tanggal_daftar')
            ->paginate(20)
            ->withQueryString();
        $fakultas = Fakultas::all();
        $programStudi = ProgramStudi::all();

        return view('pendaftaran-mahasiswa.index', compact('pendaftaran', 'fakultas', 'programStudi'));
    }

    public function exportExcel(Request $request)
    {
        $items = $this->filteredQuery($request)
            ->latest('tanggal_daftar')
            ->get();

        $headers = [
            'No. Pendaftaran',
            'Tanggal Daftar',
            'Tahun Akademik',
            'Nama Lengkap',
            'Email',
            'NIK',
            'Jenis Kelamin',
            'Jalur Masuk',
            'Program Studi',
            'Fakultas',
            'Status',
        ];

        $rows = $items->map(function ($item) {
            return [
                $item->no_pendaftaran,
                optional($item->tanggal_daftar)->format('d/m/Y H:i'),
                $item->tahun_akademik,
                $item->nama_lengkap,
                $item->email,
                $item->nik,
                $item->jenis_kelamin,
                $item->jalur_masuk,
                $item->programStudi->nama_prodi ?? '-',
                $item->programStudi->fakultas->nama_fakultas ?? '-',
                $item->status,
            ];
        });

        $html = TabularExport::htmlTable($headers, $rows);
        return TabularExport::excelResponse('pendaftaran_mahasiswa.xls', $html);
    }

    public function exportPdf(Request $request)
    {
        $items = $this->filteredQuery($request)
            ->latest('tanggal_daftar')
            ->get();

        $headers = [
            'No. Pendaftaran',
            'Tanggal Daftar',
            'Tahun Akademik',
            'Nama Lengkap',
            'Email',
            'NIK',
            'Jenis Kelamin',
            'Jalur Masuk',
            'Program Studi',
            'Fakultas',
            'Status',
        ];

        $rows = $items->map(function ($item) {
            return [
                $item->no_pendaftaran,
                optional($item->tanggal_daftar)->format('d/m/Y H:i'),
                $item->tahun_akademik,
                $item->nama_lengkap,
                $item->email,
                $item->nik,
                $item->jenis_kelamin,
                $item->jalur_masuk,
                $item->programStudi->nama_prodi ?? '-',
                $item->programStudi->fakultas->nama_fakultas ?? '-',
                $item->status,
            ];
        });

        $html = TabularExport::htmlTable($headers, $rows);
        return Pdf::loadHTML($html)->download('pendaftaran_mahasiswa.pdf');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $programStudi = ProgramStudi::with('fakultas')->get();
        $provinces = Province::all();
        
        return view('pendaftaran-mahasiswa.create', compact('programStudi', 'provinces'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_akademik' => 'required|string|max:10',
            'jalur_masuk' => 'required|in:SNBP,SNBT,Mandiri,Transfer',
            'program_studi_id' => 'required|exists:program_studi,id',
            'nama_lengkap' => 'required|string|max:100',
            'nik' => 'nullable|string|max:16',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:20',
            'status_perkawinan' => 'nullable|in:Belum Kawin,Kawin,Cerai',
            'kewarganegaraan' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
            'village_id' => 'nullable|exists:villages,id',
            'kode_pos' => 'nullable|string|max:10',
            'telepon' => 'nullable|string|max:20',
            'email' => 'required|email|max:100',
            'asal_sekolah' => 'nullable|string|max:100',
            'jurusan_sekolah' => 'nullable|string|max:50',
            'tahun_lulus' => 'nullable|string|max:4',
            'nilai_rata_rata' => 'nullable|numeric|min:0|max:100',
            'nama_ayah' => 'nullable|string|max:100',
            'pekerjaan_ayah' => 'nullable|string|max:50',
            'nama_ibu' => 'nullable|string|max:100',
            'pekerjaan_ibu' => 'nullable|string|max:50',
            'nama_wali' => 'nullable|string|max:100',
            'telepon_wali' => 'nullable|string|max:20',
            'alamat_wali' => 'nullable|string',
            'file_ids' => 'nullable|array',
            'file_ids.*' => 'exists:file_uploads,id'
        ]);

        // Generate nomor pendaftaran
        $validated['no_pendaftaran'] = PendaftaranMahasiswa::generateNoPendaftaran(
            $validated['tahun_akademik'],
            $validated['program_studi_id']
        );

        $pendaftaran = PendaftaranMahasiswa::create($validated);

        // Attach files
        if ($request->filled('file_ids')) {
            FileUpload::whereIn('id', $request->file_ids)
                ->update([
                    'fileable_type' => PendaftaranMahasiswa::class,
                    'fileable_id' => $pendaftaran->id
                ]);
        }

        return redirect()->route('pendaftaran-mahasiswa.index')
            ->with('success', 'Pendaftaran mahasiswa berhasil ditambahkan dengan No. Pendaftaran: ' . $pendaftaran->no_pendaftaran);
    }

    /**
     * Display the specified resource.
     */
    public function show(PendaftaranMahasiswa $pendaftaranMahasiswa)
    {
        $pendaftaranMahasiswa->load(['programStudi.fakultas', 'village.subRegency.regency.province', 'files', 'verifikator']);
        
        return view('pendaftaran-mahasiswa.show', compact('pendaftaranMahasiswa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PendaftaranMahasiswa $pendaftaranMahasiswa)
    {
        $programStudi = ProgramStudi::with('fakultas')->get();
        $provinces = Province::all();
        $pendaftaranMahasiswa->load(['files', 'village.subRegency.regency.province']);
        
        return view('pendaftaran-mahasiswa.edit', compact('pendaftaranMahasiswa', 'programStudi', 'provinces'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PendaftaranMahasiswa $pendaftaranMahasiswa)
    {
        $validated = $request->validate([
            'tahun_akademik' => 'required|string|max:10',
            'jalur_masuk' => 'required|in:SNBP,SNBT,Mandiri,Transfer',
            'program_studi_id' => 'required|exists:program_studi,id',
            'nama_lengkap' => 'required|string|max:100',
            'nik' => 'nullable|string|max:16',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:20',
            'status_perkawinan' => 'nullable|in:Belum Kawin,Kawin,Cerai',
            'kewarganegaraan' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
            'village_id' => 'nullable|exists:villages,id',
            'kode_pos' => 'nullable|string|max:10',
            'telepon' => 'nullable|string|max:20',
            'email' => 'required|email|max:100',
            'asal_sekolah' => 'nullable|string|max:100',
            'jurusan_sekolah' => 'nullable|string|max:50',
            'tahun_lulus' => 'nullable|string|max:4',
            'nilai_rata_rata' => 'nullable|numeric|min:0|max:100',
            'nama_ayah' => 'nullable|string|max:100',
            'pekerjaan_ayah' => 'nullable|string|max:50',
            'nama_ibu' => 'nullable|string|max:100',
            'pekerjaan_ibu' => 'nullable|string|max:50',
            'nama_wali' => 'nullable|string|max:100',
            'telepon_wali' => 'nullable|string|max:20',
            'alamat_wali' => 'nullable|string',
            'status' => 'nullable|in:Pending,Diverifikasi,Diterima,Ditolak',
            'catatan' => 'nullable|string',
            'file_ids' => 'nullable|array',
            'file_ids.*' => 'exists:file_uploads,id'
        ]);

        DB::beginTransaction();
        try {
            // Simpan status lama
            $oldStatus = $pendaftaranMahasiswa->status;

            $pendaftaranMahasiswa->update($validated);

            // Update file attachments
            if ($request->has('file_ids')) {
                $currentFileIds = $pendaftaranMahasiswa->files()->pluck('id')->toArray();
                $newFileIds = $request->file_ids ?? [];

                // Delete files that are no longer attached
                $filesToDelete = array_diff($currentFileIds, $newFileIds);
                if (!empty($filesToDelete)) {
                    FileUpload::whereIn('id', $filesToDelete)->delete();
                }

                // Attach new files
                $filesToAttach = array_diff($newFileIds, $currentFileIds);
                if (!empty($filesToAttach)) {
                    FileUpload::whereIn('id', $filesToAttach)
                        ->update([
                            'fileable_type' => PendaftaranMahasiswa::class,
                            'fileable_id' => $pendaftaranMahasiswa->id
                        ]);
                }
            }

            // Jika status berubah menjadi Diterima, otomatis export ke mahasiswa
            if ($validated['status'] === 'Diterima' && $oldStatus !== 'Diterima' && $oldStatus !== 'Dieksport') {
                $nim = $this->generateNIM($pendaftaranMahasiswa);

                // Cek apakah sudah ada mahasiswa dengan NIM ini
                $existingMahasiswa = Mahasiswa::where('nim', $nim)->first();
                if ($existingMahasiswa) {
                    DB::rollback();
                    return redirect()->back()->with('error', 'Mahasiswa dengan NIM ini sudah ada');
                }

                // Buat record mahasiswa baru
                $mahasiswa = Mahasiswa::create([
                    'nim' => $nim,
                    'nama_mahasiswa' => $pendaftaranMahasiswa->nama_lengkap,
                    'program_studi_id' => $pendaftaranMahasiswa->program_studi_id,
                    'jenis_kelamin' => $pendaftaranMahasiswa->jenis_kelamin,
                    'tempat_lahir' => $pendaftaranMahasiswa->tempat_lahir,
                    'tanggal_lahir' => $pendaftaranMahasiswa->tanggal_lahir,
                    'alamat' => $pendaftaranMahasiswa->alamat,
                    'village_id' => $pendaftaranMahasiswa->village_id,
                    'telepon' => $pendaftaranMahasiswa->telepon,
                    'email' => $pendaftaranMahasiswa->email,
                    'tahun_masuk' => substr($pendaftaranMahasiswa->tahun_akademik, 0, 4),
                    'semester' => 1,
                    'ipk' => 0,
                    'status' => 'Aktif',
                    'nama_wali' => $pendaftaranMahasiswa->nama_wali,
                    'telepon_wali' => $pendaftaranMahasiswa->telepon_wali,
                ]);

                // Copy files ke mahasiswa
                foreach ($pendaftaranMahasiswa->files as $file) {
                    FileUpload::create([
                        'original_name' => $file->original_name,
                        'file_name' => $file->file_name,
                        'file_path' => $file->file_path,
                        'file_size' => $file->file_size,
                        'mime_type' => $file->mime_type,
                        'fileable_type' => Mahasiswa::class,
                        'fileable_id' => $mahasiswa->id,
                    ]);
                }

                // Update status menjadi Dieksport
                $pendaftaranMahasiswa->update([
                    'status' => 'Dieksport',
                    'catatan' => ($pendaftaranMahasiswa->catatan ?? '') . "\n\nOtomatis dieksport ke mahasiswa dengan NIM: {$nim} pada " . now()->format('d/m/Y H:i')
                ]);

                DB::commit();
                return redirect()->route('pendaftaran-mahasiswa.index')
                    ->with('success', "Data pendaftaran berhasil diupdate dan dieksport ke mahasiswa dengan NIM: {$nim}");
            }

            DB::commit();
            return redirect()->route('pendaftaran-mahasiswa.index')
                ->with('success', 'Data pendaftaran berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Verifikasi pendaftaran
     */
    public function verifikasi(Request $request, PendaftaranMahasiswa $pendaftaranMahasiswa)
    {
        $validated = $request->validate([
            'status' => 'required|in:Diverifikasi,Diterima,Ditolak',
            'catatan' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Update status verifikasi
            $pendaftaranMahasiswa->update([
                'status' => $validated['status'],
                'catatan' => $validated['catatan'] ?? $pendaftaranMahasiswa->catatan,
                'tanggal_verifikasi' => now(),
                'verifikasi_by' => Auth::id()
            ]);

            // Jika diterima, otomatis export ke tabel mahasiswa
            if ($validated['status'] === 'Diterima') {
                $nim = $this->generateNIM($pendaftaranMahasiswa);

                // Cek apakah sudah ada mahasiswa dengan NIM ini
                $existingMahasiswa = Mahasiswa::where('nim', $nim)->first();
                if ($existingMahasiswa) {
                    DB::rollback();
                    return redirect()->back()->with('error', 'Mahasiswa dengan NIM ini sudah ada');
                }

                // Buat record mahasiswa baru
                $mahasiswa = Mahasiswa::create([
                    'nim' => $nim,
                    'nama_mahasiswa' => $pendaftaranMahasiswa->nama_lengkap,
                    'program_studi_id' => $pendaftaranMahasiswa->program_studi_id,
                    'jenis_kelamin' => $pendaftaranMahasiswa->jenis_kelamin,
                    'tempat_lahir' => $pendaftaranMahasiswa->tempat_lahir,
                    'tanggal_lahir' => $pendaftaranMahasiswa->tanggal_lahir,
                    'alamat' => $pendaftaranMahasiswa->alamat,
                    'village_id' => $pendaftaranMahasiswa->village_id,
                    'telepon' => $pendaftaranMahasiswa->telepon,
                    'email' => $pendaftaranMahasiswa->email,
                    'tahun_masuk' => substr($pendaftaranMahasiswa->tahun_akademik, 0, 4),
                    'semester' => 1,
                    'ipk' => 0,
                    'status' => 'Aktif',
                    'nama_wali' => $pendaftaranMahasiswa->nama_wali,
                    'telepon_wali' => $pendaftaranMahasiswa->telepon_wali,
                ]);

                // Copy files ke mahasiswa
                foreach ($pendaftaranMahasiswa->files as $file) {
                    FileUpload::create([
                        'original_name' => $file->original_name,
                        'file_name' => $file->file_name,
                        'file_path' => $file->file_path,
                        'file_size' => $file->file_size,
                        'mime_type' => $file->mime_type,
                        'fileable_type' => Mahasiswa::class,
                        'fileable_id' => $mahasiswa->id,
                    ]);
                }

                // Update status menjadi Dieksport
                $pendaftaranMahasiswa->update([
                    'status' => 'Dieksport',
                    'catatan' => ($pendaftaranMahasiswa->catatan ?? '') . "\n\nOtomatis dieksport ke mahasiswa dengan NIM: {$nim} pada " . now()->format('d/m/Y H:i')
                ]);

                DB::commit();
                return redirect()->back()->with('success', "Pendaftaran diterima dan berhasil dieksport ke mahasiswa dengan NIM: {$nim}");
            }

            DB::commit();
            return redirect()->back()->with('success', 'Status pendaftaran berhasil diupdate menjadi: ' . $validated['status']);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal verifikasi: ' . $e->getMessage());
        }
    }

    /**
     * Export ke tabel mahasiswa (untuk yang diterima)
     */
    public function exportToMahasiswa(PendaftaranMahasiswa $pendaftaranMahasiswa)
    {
        if ($pendaftaranMahasiswa->status !== 'Diterima') {
            return redirect()->back()->with('error', 'Hanya pendaftaran dengan status "Diterima" yang dapat dieksport');
        }

        if ($pendaftaranMahasiswa->status === 'Dieksport') {
            return redirect()->back()->with('error', 'Pendaftaran ini sudah pernah dieksport');
        }

        DB::beginTransaction();
        try {
            // Generate NIM
            $nim = $this->generateNIM($pendaftaranMahasiswa);

            // Buat data mahasiswa baru
            $mahasiswa = Mahasiswa::create([
                'program_studi_id' => $pendaftaranMahasiswa->program_studi_id,
                'nim' => $nim,
                'nama_mahasiswa' => $pendaftaranMahasiswa->nama_lengkap,
                'jenis_kelamin' => $pendaftaranMahasiswa->jenis_kelamin,
                'tempat_lahir' => $pendaftaranMahasiswa->tempat_lahir,
                'tanggal_lahir' => $pendaftaranMahasiswa->tanggal_lahir,
                'alamat' => $pendaftaranMahasiswa->alamat,
                'village_id' => $pendaftaranMahasiswa->village_id,
                'telepon' => $pendaftaranMahasiswa->telepon,
                'email' => $pendaftaranMahasiswa->email,
                'tahun_masuk' => substr($pendaftaranMahasiswa->tahun_akademik, 0, 4),
                'semester' => 1,
                'ipk' => 0.00,
                'status' => 'Aktif',
                'nama_wali' => $pendaftaranMahasiswa->nama_wali,
                'telepon_wali' => $pendaftaranMahasiswa->telepon_wali,
            ]);

            // Copy files ke mahasiswa
            foreach ($pendaftaranMahasiswa->files as $file) {
                FileUpload::create([
                    'original_name' => $file->original_name,
                    'file_name' => $file->file_name,
                    'file_path' => $file->file_path,
                    'file_size' => $file->file_size,
                    'mime_type' => $file->mime_type,
                    'fileable_type' => Mahasiswa::class,
                    'fileable_id' => $mahasiswa->id,
                ]);
            }

            // Update status pendaftaran
            $pendaftaranMahasiswa->update([
                'status' => 'Dieksport',
                'catatan' => ($pendaftaranMahasiswa->catatan ?? '') . "\n\nDieksport ke mahasiswa dengan NIM: {$nim} pada " . now()->format('d/m/Y H:i')
            ]);

            DB::commit();

            return redirect()->route('pendaftaran-mahasiswa.show', $pendaftaranMahasiswa)
                ->with('success', "Berhasil dieksport ke mahasiswa dengan NIM: {$nim}");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

    /**
     * Generate NIM untuk mahasiswa baru
     */
    private function generateNIM($pendaftaran)
    {
        $year = substr($pendaftaran->tahun_akademik, 0, 4);
        $prodi = ProgramStudi::find($pendaftaran->program_studi_id);
        $kode_prodi = $prodi ? str_pad($prodi->id, 3, '0', STR_PAD_LEFT) : '000';
        
        // Hitung jumlah mahasiswa tahun masuk ini untuk prodi ini
        $tahun_masuk = substr($pendaftaran->tahun_akademik, 0, 4);
        $count = Mahasiswa::where('tahun_masuk', $tahun_masuk)
                    ->where('program_studi_id', $pendaftaran->program_studi_id)
                    ->count();
        
        $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        
        return "{$year}{$kode_prodi}{$sequence}";
    }

    /**
     * Trash - soft deleted items
     */
    public function trash()
    {
        $pendaftaran = PendaftaranMahasiswa::onlyTrashed()
            ->with(['programStudi.fakultas'])
            ->latest('deleted_at')
            ->paginate(20);

        return view('pendaftaran-mahasiswa.trash', compact('pendaftaran'));
    }

    /**
     * Restore dari trash
     */
    public function restore($id)
    {
        $pendaftaran = PendaftaranMahasiswa::onlyTrashed()->findOrFail($id);
        $pendaftaran->restore();

        return redirect()->route('pendaftaran-mahasiswa.index')
            ->with('success', 'Data pendaftaran berhasil direstore');
    }

    /**
     * Force delete permanent
     */
    public function forceDelete($id)
    {
        $pendaftaran = PendaftaranMahasiswa::onlyTrashed()->findOrFail($id);
        
        // Delete associated files
        foreach ($pendaftaran->files as $file) {
            $file->delete();
        }
        
        $pendaftaran->forceDelete();

        return redirect()->route('pendaftaran-mahasiswa-trash')
            ->with('success', 'Data pendaftaran berhasil dihapus permanen');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PendaftaranMahasiswa $pendaftaranMahasiswa)
    {
        if ($pendaftaranMahasiswa->status === 'Dieksport') {
            return redirect()->back()->with('error', 'Data yang sudah dieksport tidak dapat dihapus');
        }

        $pendaftaranMahasiswa->delete();

        return redirect()->route('pendaftaran-mahasiswa.index')
            ->with('success', 'Data pendaftaran berhasil dihapus');
    }
}
