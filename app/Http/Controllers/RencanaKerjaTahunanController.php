<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RencanaKerjaTahunan;
use App\Models\University;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RencanaKerjaTahunanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = RencanaKerjaTahunan::with(['university', 'fakultas', 'programStudi', 'disetujuiOleh']);

        // Filter berdasarkan role
        if ($user->hasRole('admin_fakultas')) {
            $query->where('fakultas_id', $user->fakultas_id);
        } elseif ($user->hasRole('admin_prodi')) {
            $query->where('program_studi_id', $user->program_studi_id);
        }

        // Filter dari form
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('kode_rkt', 'like', '%' . $request->search . '%')
                  ->orWhere('judul_rkt', 'like', '%' . $request->search . '%');
            });
        }

        $rkt = $query->orderBy('tahun', 'desc')
                     ->orderBy('created_at', 'desc')
                     ->paginate(15);

        return view('rencana-kerja-tahunan.index', compact('rkt'));
    }

    public function create()
    {
        $user = Auth::user();
        $universities = University::orderBy('nama')->get();
        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        $prodi = ProgramStudi::orderBy('nama_prodi')->get();

        return view('rencana-kerja-tahunan.create', compact('universities', 'fakultas', 'prodi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul_rkt' => 'required|string|max:255',
            'tahun' => 'required|integer|min:2024|max:2100',
            'level' => 'required|in:Universitas,Fakultas,Prodi',
            'university_id' => 'nullable|exists:universities,id',
            'fakultas_id' => 'nullable|exists:fakultas,id',
            'program_studi_id' => 'nullable|exists:program_studi,id',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'anggaran' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $rkt = new RencanaKerjaTahunan($validated);
            $rkt->kode_rkt = $rkt->generateKodeRkt();
            $rkt->status = RencanaKerjaTahunan::STATUS_DRAFT;
            $rkt->save();

            DB::commit();
            return redirect()->route('rencana-kerja-tahunan.show', $rkt->id)
                           ->with('success', 'Rencana Kerja Tahunan berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat RKT: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $rkt = RencanaKerjaTahunan::with(['programRkt.kegiatanRkt.pencapaianRkt', 'university', 'fakultas', 'programStudi'])
                                  ->findOrFail($id);

        return view('rencana-kerja-tahunan.show', compact('rkt'));
    }

    public function edit($id)
    {
        $rkt = RencanaKerjaTahunan::findOrFail($id);

        if (!$rkt->canEdit()) {
            return back()->with('error', 'RKT tidak dapat diedit karena sudah dalam proses atau disetujui');
        }

        $universities = University::orderBy('nama')->get();
        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        $prodi = ProgramStudi::orderBy('nama_prodi')->get();

        return view('rencana-kerja-tahunan.edit', compact('rkt', 'universities', 'fakultas', 'prodi'));
    }

    public function update(Request $request, $id)
    {
        $rkt = RencanaKerjaTahunan::findOrFail($id);

        if (!$rkt->canEdit()) {
            return back()->with('error', 'RKT tidak dapat diedit');
        }

        $validated = $request->validate([
            'judul_rkt' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'anggaran' => 'nullable|numeric|min:0',
        ]);

        $rkt->update($validated);

        return redirect()->route('rencana-kerja-tahunan.show', $rkt->id)
                       ->with('success', 'RKT berhasil diperbarui');
    }

    public function destroy($id)
    {
        $rkt = RencanaKerjaTahunan::findOrFail($id);

        if (!$rkt->canDelete()) {
            return back()->with('error', 'RKT tidak dapat dihapus');
        }

        $rkt->delete();

        return redirect()->route('rencana-kerja-tahunan.index')
                       ->with('success', 'RKT berhasil dihapus');
    }

    public function submit($id)
    {
        $rkt = RencanaKerjaTahunan::findOrFail($id);

        if (!$rkt->canSubmit()) {
            return back()->with('error', 'RKT tidak dapat diajukan. Pastikan sudah ada program kerja.');
        }

        $rkt->status = RencanaKerjaTahunan::STATUS_DIAJUKAN;
        $rkt->save();

        return back()->with('success', 'RKT berhasil diajukan untuk persetujuan');
    }

    public function approve(Request $request, $id)
    {
        $rkt = RencanaKerjaTahunan::findOrFail($id);

        if (!$rkt->canApprove()) {
            return back()->with('error', 'RKT tidak dapat disetujui');
        }

        $rkt->status = RencanaKerjaTahunan::STATUS_DISETUJUI;
        $rkt->disetujui_oleh = Auth::id();
        $rkt->tanggal_disetujui = now();
        $rkt->save();

        return back()->with('success', 'RKT berhasil disetujui');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan_penolakan' => 'required|string',
        ]);

        $rkt = RencanaKerjaTahunan::findOrFail($id);

        if (!$rkt->canApprove()) {
            return back()->with('error', 'RKT tidak dapat ditolak');
        }

        $rkt->status = RencanaKerjaTahunan::STATUS_DITOLAK;
        $rkt->catatan_penolakan = $request->catatan_penolakan;
        $rkt->save();

        return back()->with('success', 'RKT ditolak');
    }
}
