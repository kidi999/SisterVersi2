<?php

namespace App\Http\Controllers;


use App\Models\Regency;
use App\Models\Province;
use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class RegencyController extends Controller
{
    /**
     * Export regencies as Excel (.xls - HTML table).
     */
    public function exportExcel(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'name');
        $order = $request->input('order', 'asc');

        $query = Regency::with('province')->withCount('subRegencies')->orderBy($sort, $order);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%")
                    ->orWhereHas('province', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%")
                            ->orWhere('code', 'like', "%$search%");
                    });
            });
        }

        $regencies = $query->get();

        $filename = 'kabupaten_kota.xls';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];

        $bom = "\xEF\xBB\xBF";
        $html = '<html><head><meta charset="UTF-8"></head><body>'
            . '<table border="1" cellspacing="0" cellpadding="4">'
            . '<thead><tr>'
            . '<th>ID</th><th>Kode</th><th>Nama Kabupaten/Kota</th><th>Provinsi</th><th>Jml Kecamatan</th>'
            . '</tr></thead><tbody>';

        foreach ($regencies as $regency) {
            $html .= '<tr>'
                . '<td>' . e((string) $regency->id) . '</td>'
                . '<td>' . e((string) $regency->code) . '</td>'
                . '<td>' . e((string) $regency->name) . '</td>'
                . '<td>' . e((string) ($regency->province ? $regency->province->name : '-')) . '</td>'
                . '<td>' . e((string) $regency->sub_regencies_count) . '</td>'
                . '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        return response($bom . $html, 200, $headers);
    }

    /**
     * Export regencies as CSV.
     */
    public function exportCsv(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'name');
        $order = $request->input('order', 'asc');
        $query = Regency::with('province')->orderBy($sort, $order);
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%")
                  ->orWhereHas('province', function($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%")
                          ->orWhere('code', 'like', "%$search%") ;
                  });
            });
        }
        $regencies = $query->get();

        $filename = 'kabupaten_kota.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($regencies) {
            $handle = fopen('php://output', 'w');
            // Header
            fputcsv($handle, ['ID', 'Kode', 'Nama Kabupaten/Kota', 'Provinsi', 'Jml Kecamatan']);
            // Data
            foreach ($regencies as $regency) {
                fputcsv($handle, [
                    $regency->id,
                    $regency->code,
                    $regency->name,
                    $regency->province ? $regency->province->name : '-',
                    $regency->subRegencies()->count(),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export regencies as PDF.
     */
    public function exportPdf(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'name');
        $order = $request->input('order', 'asc');
        $query = Regency::with('province')->orderBy($sort, $order);
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%")
                  ->orWhereHas('province', function($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%")
                          ->orWhere('code', 'like', "%$search%") ;
                  });
            });
        }
        $regencies = $query->get();
        $pdf = Pdf::loadView('wilayah.regency._export_pdf', compact('regencies'));
        return $pdf->download('kabupaten_kota.pdf');
    }

    /**
     * AJAX search for regencies (with pagination, sorting).
     */
    public function searchAjax(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'name');
        $order = $request->input('order', 'asc');
        $query = Regency::with('province')->withCount('subRegencies')->orderBy($sort, $order);
        if ($search && strlen($search) >= 2) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%")
                  ->orWhereHas('province', function($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%")
                          ->orWhere('code', 'like', "%$search%") ;
                  });
            });
        }
        $regencies = $query->paginate(10);
        return response()->json([
            'html' => view('wilayah.regency._table', compact('regencies'))->render()
        ]);
    }
    /**
     * Display a listing of the regencies.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'name');
        $order = $request->input('order', 'asc');
        $query = Regency::with('province')->withCount('subRegencies')->orderBy($sort, $order);
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%")
                  ->orWhereHas('province', function($q2) use ($search) {
                      $q2->where('name', 'like', "%$search%")
                          ->orWhere('code', 'like', "%$search%") ;
                  });
            });
        }
        $regencies = $query->paginate(10)->withQueryString();
        return view('wilayah.regency.index', compact('regencies', 'search', 'sort', 'order'));
    }

    /**
     * Show the form for creating a new regency.
     */
    public function create()
    {
        $provinces = Province::orderBy('name')->get();
        return view('wilayah.regency.create', compact('provinces'));
    }

    /**
     * Store a newly created regency in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'province_id' => 'required|exists:provinces,id',
            'code' => 'required|string|max:10|unique:regencies,code',
            'name' => 'required|string|max:100',
        ], [
            'province_id.required' => 'Provinsi harus dipilih',
            'province_id.exists' => 'Provinsi tidak valid',
            'code.required' => 'Kode kabupaten/kota harus diisi',
            'code.unique' => 'Kode kabupaten/kota sudah digunakan',
            'code.max' => 'Kode kabupaten/kota maksimal 10 karakter',
            'name.required' => 'Nama kabupaten/kota harus diisi',
            'name.max' => 'Nama kabupaten/kota maksimal 100 karakter',
        ]);

        $validated['created_by'] = Auth::user()->name;

        $regency = Regency::create($validated);

        // Link uploaded files to this regency
        if ($request->has('file_ids') && !empty($request->file_ids)) {
            $fileIds = is_array($request->file_ids) ? $request->file_ids : json_decode($request->file_ids, true);
            if (is_array($fileIds) && count($fileIds) > 0) {
                FileUpload::whereIn('id', $fileIds)
                    ->update([
                        'fileable_type' => Regency::class,
                        'fileable_id' => $regency->id
                    ]);
            }
        }

        return redirect()->route('regency.index')
            ->with('success', 'Data kabupaten/kota berhasil ditambahkan.');
    }

    /**
     * Display the specified regency.
     */
    public function show(string $id)
    {
        $regency = Regency::with(['province', 'subRegencies', 'files'])
            ->findOrFail($id);

        return view('wilayah.regency.show', compact('regency'));
    }

    /**
     * Show the form for editing the specified regency.
     */
    public function edit(string $id)
    {
        $regency = Regency::with(['files'])->findOrFail($id);
        $provinces = Province::orderBy('name')->get();

        return view('wilayah.regency.edit', compact('regency', 'provinces'));
    }

    /**
     * Update the specified regency in storage.
     */
    public function update(Request $request, string $id)
    {
        $regency = Regency::findOrFail($id);

        $validated = $request->validate([
            'province_id' => 'required|exists:provinces,id',
            'code' => 'required|string|max:10|unique:regencies,code,' . $id,
            'name' => 'required|string|max:100',
        ], [
            'province_id.required' => 'Provinsi harus dipilih',
            'province_id.exists' => 'Provinsi tidak valid',
            'code.required' => 'Kode kabupaten/kota harus diisi',
            'code.unique' => 'Kode kabupaten/kota sudah digunakan',
            'code.max' => 'Kode kabupaten/kota maksimal 10 karakter',
            'name.required' => 'Nama kabupaten/kota harus diisi',
            'name.max' => 'Nama kabupaten/kota maksimal 100 karakter',
        ]);

        $validated['updated_by'] = Auth::user()->name;

        $regency->update($validated);

        // Sync files: update existing ones and link new ones
        if ($request->has('file_ids')) {
            $fileIds = json_decode($request->file_ids, true);
            if (is_array($fileIds)) {
                // Update all files in the list to be linked to this regency
                FileUpload::whereIn('id', $fileIds)
                    ->update([
                        'fileable_type' => Regency::class,
                        'fileable_id' => $regency->id
                    ]);

                // Remove orphaned files (files that were previously linked but not in the new list)
                FileUpload::where('fileable_type', Regency::class)
                    ->where('fileable_id', $regency->id)
                    ->whereNotIn('id', $fileIds)
                    ->delete();
            }
        }

        return redirect()->route('regency.index')
            ->with('success', 'Data kabupaten/kota berhasil diperbarui.');
    }

    /**
     * Remove the specified regency from storage (soft delete).
     */
    public function destroy(string $id)
    {
        $regency = Regency::findOrFail($id);
        $regency->deleted_by = Auth::user()->name;
        $regency->save();
        $regency->delete();

        return redirect()->route('regency.index')
            ->with('success', 'Data kabupaten/kota berhasil dihapus.');
    }

    /**
     * Display a listing of trashed regencies (super_admin only).
     */
    public function trash()
    {
        if (!Auth::user()->hasRole(['super_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $regencies = Regency::onlyTrashed()
            ->with(['province'])
            ->get();

        return view('wilayah.regency.trash', compact('regencies'));
    }

    /**
     * Restore the specified regency from trash (super_admin only).
     */
    public function restore(string $id)
    {
        if (!Auth::user()->hasRole(['super_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $regency = Regency::onlyTrashed()->findOrFail($id);
        $regency->restore();

        return redirect()->route('regency.trash')
            ->with('success', 'Data kabupaten/kota berhasil dipulihkan.');
    }

    /**
     * Permanently delete the specified regency (super_admin only).
     */
    public function forceDelete(string $id)
    {
        if (!Auth::user()->hasRole(['super_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $regency = Regency::onlyTrashed()->findOrFail($id);
        $regency->forceDelete();

        return redirect()->route('regency.trash')
            ->with('success', 'Data kabupaten/kota berhasil dihapus permanen.');
    }
}
