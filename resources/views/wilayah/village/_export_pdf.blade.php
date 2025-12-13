@php
    $no = 1;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Desa/Kelurahan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 4px 8px; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Data Desa/Kelurahan</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Desa/Kelurahan</th>
                <th>Kecamatan</th>
                <th>Kabupaten/Kota</th>
                <th>Provinsi</th>
                <th>Kode Pos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($villages as $village)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $village->code }}</td>
                <td>{{ $village->name }}</td>
                <td>{{ $village->subRegency->name ?? '' }}</td>
                <td>{{ $village->subRegency->regency->type ?? '' }} {{ $village->subRegency->regency->name ?? '' }}</td>
                <td>{{ $village->subRegency->regency->province->name ?? '' }}</td>
                <td>{{ $village->postal_code }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
