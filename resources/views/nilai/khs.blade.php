<!DOCTYPE html>
<html>
<head>
    <title>KHS - {{ $mahasiswa->nama }}</title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16pt;
        }
        .header h3 {
            margin: 5px 0;
            font-size: 14pt;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 3px 10px;
        }
        .nilai-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .nilai-table th, .nilai-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .nilai-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .summary-box {
            border: 2px solid #000;
            padding: 15px;
            margin-top: 20px;
            background-color: #f9f9f9;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            margin-top: 80px;
            border-top: 1px solid #000;
            display: inline-block;
            width: 200px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
            🖨️ Cetak KHS
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; margin-left: 10px;">
            ❌ Tutup
        </button>
    </div>

    <div class="header">
        <h2>UNIVERSITAS ISLAM NEGERI SUMATERA UTARA</h2>
        <h3>KARTU HASIL STUDI (KHS)</h3>
        <p style="margin: 5px 0;">Tahun Ajaran {{ $tahunAjaran }} - Semester {{ $semester }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="150"><strong>NIM</strong></td>
            <td width="10">:</td>
            <td>{{ $mahasiswa->nim }}</td>
        </tr>
        <tr>
            <td><strong>Nama</strong></td>
            <td>:</td>
            <td>{{ $mahasiswa->nama }}</td>
        </tr>
        <tr>
            <td><strong>Program Studi</strong></td>
            <td>:</td>
            <td>{{ $mahasiswa->programStudi->nama }}</td>
        </tr>
        <tr>
            <td><strong>Fakultas</strong></td>
            <td>:</td>
            <td>{{ $mahasiswa->programStudi->fakultas->nama }}</td>
        </tr>
    </table>

    <table class="nilai-table">
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="80">Kode MK</th>
                <th>Nama Mata Kuliah</th>
                <th width="50">SKS</th>
                <th width="70">Nilai Angka</th>
                <th width="60">Nilai Huruf</th>
                <th width="60">Bobot</th>
                <th width="80">SKS × Bobot</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nilaiList as $nilai)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $nilai->krs->kelas->mataKuliah->kode }}</td>
                <td>{{ $nilai->krs->kelas->mataKuliah->nama }}</td>
                <td class="text-center">{{ $nilai->krs->kelas->mataKuliah->sks }}</td>
                <td class="text-center">{{ number_format($nilai->nilai_akhir, 2) }}</td>
                <td class="text-center"><strong>{{ $nilai->nilai_huruf }}</strong></td>
                <td class="text-center">{{ number_format($nilai->bobot, 2) }}</td>
                <td class="text-center">{{ number_format($nilai->krs->kelas->mataKuliah->sks * $nilai->bobot, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <table width="100%">
            <tr>
                <td width="70%"><strong>TOTAL SKS SEMESTER INI</strong></td>
                <td class="text-right"><strong>{{ $totalSks }} SKS</strong></td>
            </tr>
            <tr>
                <td><strong>INDEKS PRESTASI SEMESTER (IPS)</strong></td>
                <td class="text-right"><strong>{{ number_format($ips, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <p>Mahasiswa,</p>
            <div class="signature-line"></div>
            <p><strong>{{ $mahasiswa->nama }}</strong></p>
            <p>NIM: {{ $mahasiswa->nim }}</p>
        </div>
        <div class="signature-box">
            <p>Dosen Wali,</p>
            <div class="signature-line"></div>
            <p><strong>(...................................)</strong></p>
        </div>
    </div>

    <p style="margin-top: 50px; font-size: 10pt; font-style: italic;">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}
    </p>
</body>
</html>
