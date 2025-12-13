<?php

namespace App\Http\Controllers;

use App\Models\PembayaranMahasiswa;
use App\Models\TagihanMahasiswa;
use App\Models\FileUpload;
use App\Support\TabularExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PembayaranMahasiswaController extends Controller
{
    private function filteredQuery(Request $request)
    {
        $query = PembayaranMahasiswa::with([
            'mahasiswa.programStudi.fakultas',
            'tagihanMahasiswa.jenisPembayaran',
            'verifiedBy'
        ]);

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_pembayaran', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($q2) use ($search) {
                        $q2->where('nim', 'like', "%{$search}%")
                            ->orWhere('nama_mahasiswa', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status verifikasi
        if ($request->filled('status_verifikasi')) {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        // Filter by tanggal
        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_bayar', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_bayar', '<=', $request->tanggal_sampai);
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
     * Display a listing of pembayaran (for admin)
     */
    public function index(Request $request)
    {
        $pembayaran = $this->filteredQuery($request)
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('pembayaran-mahasiswa.index', compact('pembayaran'));
    }

    public function exportExcel(Request $request)
    {
        $items = $this->filteredQuery($request)->orderBy('created_at', 'desc')->get();

        $headers = [
            'No. Pembayaran',
            'Tanggal Bayar',
            'NIM',
            'Nama Mahasiswa',
            'Program Studi',
            'Fakultas',
            'No. Tagihan',
            'Jenis Pembayaran',
            'Jumlah Bayar',
            'Metode Pembayaran',
            'Status Verifikasi',
            'Verified By',
            'Verified At',
        ];

        $rows = $items->map(function ($p) {
            return [
                $p->nomor_pembayaran,
                optional($p->tanggal_bayar)->format('d/m/Y'),
                $p->mahasiswa->nim ?? '-',
                $p->mahasiswa->nama_mahasiswa ?? '-',
                $p->mahasiswa->programStudi->nama_prodi ?? '-',
                $p->mahasiswa->programStudi->fakultas->nama_fakultas ?? '-',
                $p->tagihanMahasiswa->nomor_tagihan ?? '-',
                $p->tagihanMahasiswa->jenisPembayaran->nama ?? '-',
                $p->jumlah_bayar,
                $p->metode_pembayaran,
                $p->status_verifikasi,
                $p->verifiedBy->name ?? '-',
                optional($p->verified_at)->format('d/m/Y H:i'),
            ];
        });

        $html = TabularExport::htmlTable($headers, $rows);
        return TabularExport::excelResponse('pembayaran_mahasiswa.xls', $html);
    }

    public function exportPdf(Request $request)
    {
        $items = $this->filteredQuery($request)->orderBy('created_at', 'desc')->get();

        $headers = [
            'No. Pembayaran',
            'Tanggal Bayar',
            'NIM',
            'Nama Mahasiswa',
            'Program Studi',
            'Fakultas',
            'No. Tagihan',
            'Jenis Pembayaran',
            'Jumlah Bayar',
            'Metode Pembayaran',
            'Status Verifikasi',
            'Verified By',
            'Verified At',
        ];

        $rows = $items->map(function ($p) {
            return [
                $p->nomor_pembayaran,
                optional($p->tanggal_bayar)->format('d/m/Y'),
                $p->mahasiswa->nim ?? '-',
                $p->mahasiswa->nama_mahasiswa ?? '-',
                $p->mahasiswa->programStudi->nama_prodi ?? '-',
                $p->mahasiswa->programStudi->fakultas->nama_fakultas ?? '-',
                $p->tagihanMahasiswa->nomor_tagihan ?? '-',
                $p->tagihanMahasiswa->jenisPembayaran->nama ?? '-',
                $p->jumlah_bayar,
                $p->metode_pembayaran,
                $p->status_verifikasi,
                $p->verifiedBy->name ?? '-',
                optional($p->verified_at)->format('d/m/Y H:i'),
            ];
        });

        $html = TabularExport::htmlTable($headers, $rows);
        return Pdf::loadHTML($html)->download('pembayaran_mahasiswa.pdf');
    }

    /**
     * Show the form for creating a new pembayaran (admin input pembayaran mahasiswa)
     */
    public function create(Request $request)
    {
        $tagihanId = $request->tagihan_id;
        $tagihan = null;

        $tagihanOptions = collect();

        if ($tagihanId) {
            $tagihan = TagihanMahasiswa::with([
                'mahasiswa.programStudi',
                'jenisPembayaran',
                'tahunAkademik',
                'semester'
            ])->findOrFail($tagihanId);
        } else {
            $query = TagihanMahasiswa::with([
                'mahasiswa.programStudi.fakultas',
                'jenisPembayaran',
                'tahunAkademik',
                'semester'
            ])
                ->where('sisa_tagihan', '>', 0)
                ->orderBy('created_at', 'desc');

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

            $tagihanOptions = $query->limit(200)->get();
        }

        return view('pembayaran-mahasiswa.create', compact('tagihan', 'tagihanOptions'));
    }

    /**
     * Store a newly created pembayaran
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tagihan_mahasiswa_id' => 'required|exists:tagihan_mahasiswa,id',
            'jumlah_bayar' => 'required|numeric|min:0',
            'tanggal_bayar' => 'required|date',
            'waktu_bayar' => 'nullable|date_format:H:i',
            'metode_pembayaran' => 'required|in:Transfer Bank,Tunai,Virtual Account,E-Wallet,Kartu Kredit/Debit,Lainnya',
            'nama_bank' => 'nullable|string|max:100',
            'nomor_rekening' => 'nullable|string|max:50',
            'nama_pemilik_rekening' => 'nullable|string|max:100',
            'nomor_referensi' => 'nullable|string|max:100',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'keterangan' => 'nullable|string',
            'file_ids' => 'nullable|array',
            'file_ids.*' => 'exists:file_uploads,id',
        ]);

        $tagihan = TagihanMahasiswa::findOrFail($validated['tagihan_mahasiswa_id']);

        // Check if payment exceeds remaining balance
        if ($validated['jumlah_bayar'] > $tagihan->sisa_tagihan) {
            return back()->with('error', 'Jumlah pembayaran melebihi sisa tagihan.')
                        ->withInput();
        }

        DB::beginTransaction();
        try {
            $validated['mahasiswa_id'] = $tagihan->mahasiswa_id;
            $validated['nomor_pembayaran'] = PembayaranMahasiswa::generateNomorPembayaran();
            $validated['status_verifikasi'] = 'Diverifikasi'; // Auto verify for admin input
            $validated['verified_by'] = Auth::id();
            $validated['verified_at'] = now();
            $validated['created_by'] = Auth::user()->name;

            // Handle file upload
            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('bukti_pembayaran', $filename, 'public');
                $validated['bukti_pembayaran'] = $path;
            }

            $pembayaran = PembayaranMahasiswa::create($validated);

            if ($request->filled('file_ids') && is_array($request->file_ids)) {
                FileUpload::whereIn('id', $request->file_ids)
                    ->update([
                        'fileable_type' => PembayaranMahasiswa::class,
                        'fileable_id' => $pembayaran->id,
                    ]);
            }

            // Update tagihan
            $tagihan->jumlah_dibayar += $validated['jumlah_bayar'];
            $tagihan->updated_by = Auth::user()->name;
            $tagihan->updateStatus();

            DB::commit();
            return redirect()->route('pembayaran-mahasiswa.index')
                           ->with('success', 'Pembayaran berhasil dicatat.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal mencatat pembayaran: ' . $e->getMessage())
                        ->withInput();
        }
    }

    /**
     * Display the specified pembayaran
     */
    public function show(PembayaranMahasiswa $pembayaranMahasiswa)
    {
        $pembayaranMahasiswa->load([
            'mahasiswa.programStudi.fakultas',
            'tagihanMahasiswa.jenisPembayaran',
            'tagihanMahasiswa.tahunAkademik',
            'tagihanMahasiswa.semester',
            'verifiedBy',
            'files',
        ]);

        return view('pembayaran-mahasiswa.show', compact('pembayaranMahasiswa'));
    }

    /**
     * Verify pembayaran
     */
    public function verify(Request $request, PembayaranMahasiswa $pembayaranMahasiswa)
    {
        if ($pembayaranMahasiswa->status_verifikasi !== 'Pending') {
            return back()->with('error', 'Pembayaran ini sudah diverifikasi atau ditolak.');
        }

        $validated = $request->validate([
            'catatan_verifikasi' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $pembayaranMahasiswa->status_verifikasi = 'Diverifikasi';
            $pembayaranMahasiswa->verified_by = Auth::id();
            $pembayaranMahasiswa->verified_at = now();
            $pembayaranMahasiswa->catatan_verifikasi = $validated['catatan_verifikasi'] ?? null;
            $pembayaranMahasiswa->updated_by = Auth::user()->name;
            $pembayaranMahasiswa->save();

            // Update tagihan
            $tagihan = $pembayaranMahasiswa->tagihanMahasiswa;
            $tagihan->jumlah_dibayar += $pembayaranMahasiswa->jumlah_bayar;
            $tagihan->updated_by = Auth::user()->name;
            $tagihan->updateStatus();

            DB::commit();
            return back()->with('success', 'Pembayaran berhasil diverifikasi.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal memverifikasi pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Reject pembayaran
     */
    public function reject(Request $request, PembayaranMahasiswa $pembayaranMahasiswa)
    {
        if ($pembayaranMahasiswa->status_verifikasi !== 'Pending') {
            return back()->with('error', 'Pembayaran ini sudah diverifikasi atau ditolak.');
        }

        $validated = $request->validate([
            'catatan_verifikasi' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $pembayaranMahasiswa->status_verifikasi = 'Ditolak';
            $pembayaranMahasiswa->verified_by = Auth::id();
            $pembayaranMahasiswa->verified_at = now();
            $pembayaranMahasiswa->catatan_verifikasi = $validated['catatan_verifikasi'];
            $pembayaranMahasiswa->updated_by = Auth::user()->name;
            $pembayaranMahasiswa->save();

            DB::commit();
            return back()->with('success', 'Pembayaran ditolak.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menolak pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Display mahasiswa's own tagihan and pembayaran
     */
    public function myPayments()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            abort(403, 'Anda tidak terdaftar sebagai mahasiswa.');
        }

        $tagihan = TagihanMahasiswa::where('mahasiswa_id', $mahasiswa->id)
                                   ->with([
                                       'jenisPembayaran',
                                       'tahunAkademik',
                                       'semester',
                                       'pembayaran'
                                   ])
                                   ->orderBy('tanggal_jatuh_tempo', 'desc')
                                   ->get();

        $pembayaran = PembayaranMahasiswa::where('mahasiswa_id', $mahasiswa->id)
                                         ->with([
                                             'tagihanMahasiswa.jenisPembayaran',
                                             'verifiedBy'
                                         ])
                                         ->orderBy('tanggal_bayar', 'desc')
                                         ->get();

        return view('pembayaran-mahasiswa.my-payments', compact('tagihan', 'pembayaran'));
    }

    /**
     * Mahasiswa upload bukti pembayaran
     */
    public function uploadBukti(Request $request)
    {
        $validated = $request->validate([
            'tagihan_mahasiswa_id' => 'required|exists:tagihan_mahasiswa,id',
            'jumlah_bayar' => 'required|numeric|min:0',
            'tanggal_bayar' => 'required|date',
            'waktu_bayar' => 'nullable|date_format:H:i',
            'metode_pembayaran' => 'required|in:Transfer Bank,Virtual Account,E-Wallet,Kartu Kredit/Debit',
            'nama_bank' => 'nullable|string|max:100',
            'nomor_rekening' => 'nullable|string|max:50',
            'nama_pemilik_rekening' => 'nullable|string|max:100',
            'nomor_referensi' => 'nullable|string|max:100',
            'bukti_pembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        $user = Auth::user();
        $tagihan = TagihanMahasiswa::findOrFail($validated['tagihan_mahasiswa_id']);

        // Verify ownership
        if ($tagihan->mahasiswa_id !== $user->mahasiswa_id) {
            abort(403, 'Anda tidak memiliki akses ke tagihan ini.');
        }

        // Check if payment exceeds remaining balance
        if ($validated['jumlah_bayar'] > $tagihan->sisa_tagihan) {
            return back()->with('error', 'Jumlah pembayaran melebihi sisa tagihan.')
                        ->withInput();
        }

        DB::beginTransaction();
        try {
            $validated['mahasiswa_id'] = $user->mahasiswa_id;
            $validated['nomor_pembayaran'] = PembayaranMahasiswa::generateNomorPembayaran();
            $validated['status_verifikasi'] = 'Pending';
            $validated['created_by'] = $user->name;

            // Handle file upload
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('bukti_pembayaran', $filename, 'public');
            $validated['bukti_pembayaran'] = $path;

            PembayaranMahasiswa::create($validated);

            DB::commit();
            return back()->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal mengupload bukti pembayaran: ' . $e->getMessage())
                        ->withInput();
        }
    }
}
