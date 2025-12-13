@php $no = 1; @endphp
<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode</th>
            <th>Nama Provinsi</th>
            <th>Jumlah Kabupaten/Kota</th>
        </tr>
    </thead>
    <tbody>
        @foreach($provinces as $province)
        <tr>
            <td>{{ $no++ }}</td>
            <td>{{ $province->code }}</td>
            <td>{{ $province->name }}</td>
            <td>{{ $province->regencies_count }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
