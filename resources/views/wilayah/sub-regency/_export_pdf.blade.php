<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Data Kecamatan</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Data Kecamatan</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Kecamatan</th>
                <th>Kabupaten/Kota</th>
                <th>Provinsi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subRegencies as $i => $subRegency)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $subRegency->code }}</td>
                <td>{{ $subRegency->name }}</td>
                <td>{{ $subRegency->regency ? $subRegency->regency->name : '-' }}</td>
                <td>{{ $subRegency->regency && $subRegency->regency->province ? $subRegency->regency->province->name : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
