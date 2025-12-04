<?php

namespace App\Http\Controllers;

use App\Models\Village;
use App\Models\SubRegency;
use App\Models\Regency;
use App\Models\Province;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Get regencies by province
     */
    public function getRegencies($provinceId)
    {
        $regencies = Regency::where('province_id', $provinceId)->get();
        return response()->json($regencies);
    }

    /**
     * Get sub regencies by regency
     */
    public function getSubRegencies($regencyId)
    {
        $subRegencies = SubRegency::where('regency_id', $regencyId)->get();
        return response()->json($subRegencies);
    }

    /**
     * Get villages by sub regency
     */
    public function getVillages($subRegencyId)
    {
        $villages = Village::where('sub_regency_id', $subRegencyId)->get();
        return response()->json($villages);
    }
}
