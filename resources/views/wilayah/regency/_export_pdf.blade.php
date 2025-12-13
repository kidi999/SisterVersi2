<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Data Kabupaten/Kota</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Data Kabupaten/Kota</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Kabupaten/Kota</th>
                <th>Provinsi</th>
                <th>Jml Kecamatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($regencies as $i => $regency)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $regency->code }}</td>
                <td>{{ $regency->name }}</td>
                <td>{{ $regency->province ? $regency->province->name : '-' }}</td>
                <td>{{ $regency->subRegencies->count() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
