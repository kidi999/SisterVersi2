<?php

namespace App\Http\Controllers;
use App\Models\SubRegency;
use App\Models\Regency;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubRegencyController extends Controller
{
    /**
     * Export data kecamatan ke Excel (.xls - HTML table)
     */
    public function exportExcel(Request $request)
    {
        $query = SubRegency::with('regency.province')->orderBy('name');
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%$search%")
                ->orWhere('code', 'like', "%$search%")
                ->orWhereHas('regency', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhereHas('province', function ($qq) use ($search) {
                            $qq->where('name', 'like', "%$search%");
                        });
                });
        }
        $subRegencies = $query->get();

        $filename = 'sub_regencies_' . now()->format('Ymd_His') . '.xls';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'max-age=0',
        ];

        $bom = "\xEF\xBB\xBF";
        $html = '<html><head><meta charset="UTF-8"></head><body>'
            . '<table border="1" cellspacing="0" cellpadding="4">'
            . '<thead><tr>'
            . '<th>No</th><th>Kode</th><th>Nama Kecamatan</th><th>Kabupaten/Kota</th><th>Provinsi</th>'
            . '</tr></thead><tbody>';

        foreach ($subRegencies as $i => $sub) {
            $html .= '<tr>'
                . '<td>' . e((string) ($i + 1)) . '</td>'
                . '<td>' . e((string) $sub->code) . '</td>'
                . '<td>' . e((string) $sub->name) . '</td>'
                . '<td>' . e((string) ($sub->regency ? $sub->regency->name : '')) . '</td>'
                . '<td>' . e((string) ($sub->regency && $sub->regency->province ? $sub->regency->province->name : '')) . '</td>'
                . '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        return response($bom . $html, 200, $headers);
    }

    /**
     * Export data kecamatan ke CSV
     */
    public function exportCsv()
    {
        $subRegencies = \App\Models\SubRegency::with('regency.province')->orderBy('name')->get();
        $filename = 'sub_regencies_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        $callback = function () use ($subRegencies) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'Kode', 'Nama Kecamatan', 'Kabupaten/Kota', 'Provinsi']);
            foreach ($subRegencies as $i => $sub) {
                fputcsv($handle, [
                    $i + 1,
                    $sub->code,
                    $sub->name,
                    $sub->regency ? $sub->regency->name : '',
                    $sub->regency && $sub->regency->province ? $sub->regency->province->name : ''
                ]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export data kecamatan ke PDF
     */
    public function exportPdf()
    {
        $subRegencies = \App\Models\SubRegency::with('regency.province')->orderBy('name')->get();
        $pdf = \PDF::loadView('wilayah.sub-regency._export_pdf', compact('subRegencies'));
        return $pdf->download('sub_regencies_' . now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Tampilkan daftar kecamatan dengan paginasi
     */
    public function index(Request $request)
    {
        $query = \App\Models\SubRegency::with('regency.province')->orderBy('name');
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%$search%")
                ->orWhere('code', 'like', "%$search%")
                ->orWhereHas('regency', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhereHas('province', function ($qq) use ($search) {
                            $qq->where('name', 'like', "%$search%");
                        });
                });
        }
        $subRegencies = $query->paginate(10)->withQueryString();
        $provinces = Province::orderBy('name')->get();
        $regencies = Regency::orderBy('name')->get();
        return view('sub-regency.index', compact('subRegencies', 'provinces', 'regencies'));
    }
    // ...existing code...

    public function create()
    {
        $provinces = Province::orderBy('name')->get();
        $regencies = Regency::orderBy('name')->get();
        
        return view('sub-regency.create', compact('provinces', 'regencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'regency_id' => 'required|exists:regencies,id',
            'code' => 'required|string|max:10|unique:sub_regencies,code',
            'name' => 'required|string|max:100',
        ], [
            'regency_id.required' => 'Kabupaten/Kota harus dipilih',
            'regency_id.exists' => 'Kabupaten/Kota tidak valid',
            'code.required' => 'Kode kecamatan harus diisi',
            'code.unique' => 'Kode kecamatan sudah digunakan',
            'name.required' => 'Nama kecamatan harus diisi',
        ]);

        $validated['created_by'] = Auth::user()->name;

        $subRegency = SubRegency::create($validated);

        // Handle new file uploads
        if ($request->has('file_ids')) {
            $fileIds = is_array($request->file_ids) ? $request->file_ids : json_decode($request->file_ids, true);
            if (is_array($fileIds) && !empty($fileIds)) {
                \App\Models\FileUpload::whereIn('id', $fileIds)
                    ->where(function($query) {
                        $query->whereNull('fileable_id')
                              ->orWhere('fileable_id', 0);
                    })
                    ->update([
                        'fileable_id' => $subRegency->id,
                        'fileable_type' => 'App\\Models\\SubRegency'
                    ]);
            }
        }

        return redirect()->route('sub-regency.index')
            ->with('success', 'Kecamatan berhasil ditambahkan.');
    }

    public function show(SubRegency $subRegency)
    {
        $subRegency->load(['regency.province', 'villages', 'files']);
        return view('sub-regency.show', compact('subRegency'));
    }

    public function edit(SubRegency $subRegency)
    {
        $subRegency->load('files');
        $provinces = Province::orderBy('name')->get();
        $regencies = Regency::orderBy('name')->get();
        
        return view('sub-regency.edit', compact('subRegency', 'provinces', 'regencies'));
    }

    public function update(Request $request, SubRegency $subRegency)
    {
        $validated = $request->validate([
            'regency_id' => 'required|exists:regencies,id',
            'code' => 'required|string|max:10|unique:sub_regencies,code,' . $subRegency->id,
            'name' => 'required|string|max:100',
        ], [
            'regency_id.required' => 'Kabupaten/Kota harus dipilih',
            'regency_id.exists' => 'Kabupaten/Kota tidak valid',
            'code.required' => 'Kode kecamatan harus diisi',
            'code.unique' => 'Kode kecamatan sudah digunakan',
            'name.required' => 'Nama kecamatan harus diisi',
        ]);

        $validated['updated_by'] = Auth::user()->name;

        $subRegency->update($validated);

        // Handle file uploads
        if ($request->has('file_ids')) {
            $fileIds = json_decode($request->file_ids, true);
            if (is_array($fileIds) && !empty($fileIds)) {
                \App\Models\FileUpload::whereIn('id', $fileIds)
                    ->where(function($query) use ($subRegency) {
                        $query->whereNull('fileable_id')
                              ->orWhere('fileable_id', 0)
                              ->orWhere('fileable_id', $subRegency->id);
                    })
                    ->update([
                        'fileable_id' => $subRegency->id,
                        'fileable_type' => 'App\\Models\\SubRegency'
                    ]);
            }
        }

        return redirect()->route('sub-regency.index')
            ->with('success', 'Kecamatan berhasil diperbarui.');
    }

    public function destroy(SubRegency $subRegency)
    {
        $subRegency->deleted_by = Auth::user()->name;
        $subRegency->save();
        $subRegency->delete();

        return redirect()->route('sub-regency.index')
            ->with('success', 'Kecamatan berhasil dihapus.');
    }

    public function trash()
    {
        $subRegencies = SubRegency::onlyTrashed()
            ->with(['regency.province'])
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('sub-regency.trash', compact('subRegencies'));
    }

    public function restore($id)
    {
        $subRegency = SubRegency::onlyTrashed()->findOrFail($id);
        $subRegency->restore();

        return response()->json([
            'message' => 'Kecamatan berhasil dipulihkan.'
        ]);
    }

    public function forceDelete($id)
    {
        $subRegency = SubRegency::onlyTrashed()->findOrFail($id);
        
        // Delete all files
        foreach ($subRegency->files as $file) {
            if (\Storage::exists($file->file_path)) {
                \Storage::delete($file->file_path);
            }
            $file->delete();
        }
        
        $subRegency->forceDelete();

        return response()->json([
            'message' => 'Kecamatan berhasil dihapus permanen.'
        ]);
    }

    public function getRegenciesByProvince($provinceId)
    {
        $regencies = Regency::where('province_id', $provinceId)
            ->orderBy('name')
            ->get(['id', 'name', 'type']);

        return response()->json($regencies);
    }
}
