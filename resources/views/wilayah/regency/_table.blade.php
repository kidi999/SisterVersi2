@foreach($regencies as $index => $regency)
<tr>
    <td>{{ ($regencies->currentPage() - 1) * $regencies->perPage() + $loop->iteration }}</td>
    <td><span class="badge bg-primary">{{ $regency->code }}</span></td>
    <td>{{ $regency->province ? $regency->province->name : '-' }}</td>
    <td>{{ $regency->name }}</td>
    <td class="text-center">
        <span class="badge bg-info">{{ $regency->sub_regencies_count ?? $regency->subRegencies->count() }}</span>
    </td>
    <td class="text-center">
        <div class="btn-group btn-group-sm" role="group">
            <a href="{{ route('regency.show', $regency) }}" class="btn btn-info" title="Detail">
                <i class="bi bi-eye"></i>
            </a>
            <a href="{{ route('regency.edit', $regency) }}" class="btn btn-warning" title="Edit">
                <i class="bi bi-pencil"></i>
            </a>
            <form action="{{ route('regency.destroy', $regency) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kabupaten/kota ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" title="Hapus">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
@endforeach
@if($regencies->isEmpty())
<tr>
    <td colspan="6" class="text-center text-muted">Tidak ada data kabupaten/kota</td>
</tr>
@endif
