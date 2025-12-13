<?php

namespace App\Exports;

use App\Models\Province;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProvincesExport implements FromCollection, WithHeadings
{
    protected $search;
    public function __construct($search = null)
    {
        $this->search = $search;
    }
    public function collection()
    {
        $query = Province::query()->orderBy('name');
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%");
            });
        }
        return $query->get(['id', 'code', 'name']);
    }
    public function headings(): array
    {
        return ['ID', 'Kode', 'Nama Provinsi'];
    }
}
