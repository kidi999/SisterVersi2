<?php

namespace App\Exports;

use App\Models\Province;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ProvincesExportView implements FromView
{
    protected $search;
    public function __construct($search = null)
    {
        $this->search = $search;
    }
    public function view(): View
    {
        $query = Province::withCount('regencies')->orderBy('name');
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%");
            });
        }
        $provinces = $query->get();
        return view('wilayah.provinsi._export_pdf', compact('provinces'));
    }
}
