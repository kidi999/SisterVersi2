<!DOCTYPE html>
<html>
<head>
    <title>Transkrip - {{ $mahasiswa->nama }}</title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
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
        .semester-header {
            background-color: #e0e0e0;
            padding: 8px;
            margin-top: 15px;
            margin-bottom: 10px;
            font-weight: bold;
            border-left: 4px solid #333;
        }
        .nilai-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .nilai-table th, .nilai-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        .nilai-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 10pt;
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
        .ipk-box {
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
            margin: 20px 0;
            padding: 20px;
            border: 3px solid #000;
            background-color: #fff3cd;
        }
        .signature-section {
            margin-top: 40px;
            text-align: right;
        }
        .signature-box {
            display: inline-block;
            text-align: center;
        }
        .signature-line {
            margin-top: 80px;
            border-top: 1px solid #000;
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
            🖨️ Cetak Transkrip
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; margin-left: 10px;">
            ❌ Tutup
        </button>
    </div>

    <div class="header">
        <h2>UNIVERSITAS ISLAM NEGERI SUMATERA UTARA</h2>
        <h3>TRANSKRIP NILAI AKADEMIK</h3>
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

    @foreach($nilaiGrouped as $semesterKey => $nilaiSemester)
        @php
            list($tahunAjaran, $semester) = explode('-', $semesterKey);
            $totalSksSemester = 0;
            $totalBobotSksSemester = 0;
            foreach ($nilaiSemester as $nilai) {
                $sks = $nilai->krs->kelas->mataKuliah->sks;
                $totalSksSemester += $sks;
                $totalBobotSksSemester += ($nilai->bobot * $sks);
            }
            $ipsSemester = $totalSksSemester > 0 ? round($totalBobotSksSemester / $totalSksSemester, 2) : 0;
        @endphp

        <div class="semester-header">
            Tahun Ajaran {{ $tahunAjaran }} - Semester {{ $semester }}
        </div>

        <table class="nilai-table">
            <thead>
                <tr>
                    <th width="30">No</th>
                    <th width="70">Kode MK</th>
                    <th>Nama Mata Kuliah</th>
                    <th width="40">SKS</th>
                    <th width="60">Nilai</th>
                    <th width="50">Huruf</th>
                    <th width="50">Bobot</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nilaiSemester as $nilai)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $nilai->krs->kelas->mataKuliah->kode }}</td>
                    <td>{{ $nilai->krs->kelas->mataKuliah->nama }}</td>
                    <td class="text-center">{{ $nilai->krs->kelas->mataKuliah->sks }}</td>
                    <td class="text-center">{{ number_format($nilai->nilai_akhir, 2) }}</td>
                    <td class="text-center"><strong>{{ $nilai->nilai_huruf }}</strong></td>
                    <td class="text-center">{{ number_format($nilai->bobot, 2) }}</td>
                </tr>
                @endforeach
                <tr style="background-color: #f9f9f9;">
                    <td colspan="3" class="text-right"><strong>IPS Semester Ini:</strong></td>
                    <td class="text-center"><strong>{{ $totalSksSemester }} SKS</strong></td>
                    <td colspan="2" class="text-center"><strong>{{ number_format($ipsSemester, 2) }}</strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    @endforeach

    <div class="summary-box">
        <table width="100%">
            <tr>
                <td width="70%"><strong>TOTAL SKS KUMULATIF</strong></td>
                <td class="text-right"><strong>{{ $totalSksKumulatif }} SKS</strong></td>
            </tr>
        </table>
    </div>

    <div class="ipk-box">
        INDEKS PRESTASI KUMULATIF (IPK)
        <div style="margin-top: 10px;">{{ number_format($ipk, 2) }}</div>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <p>Medan, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
            <p>Kepala Program Studi,</p>
            <div class="signature-line"></div>
            <p><strong>(...................................)</strong></p>
        </div>
    </div>

    <p style="margin-top: 50px; font-size: 9pt; font-style: italic;">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}
    </p>

    <p style="font-size: 9pt; color: #666; border-top: 1px solid #ccc; padding-top: 10px; margin-top: 20px;">
        <strong>Catatan:</strong> Transkrip ini dicetak otomatis dari Sistem Informasi Akademik (SISTER).
        Untuk keperluan resmi, mohon hubungi Bagian Akademik untuk mendapatkan transkrip yang telah dilegalisir.
    </p>
</body>
</html>
