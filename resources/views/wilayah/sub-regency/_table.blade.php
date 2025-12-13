@foreach($subRegencies as $index => $subRegency)
<tr>
    <td>{{ ($subRegencies instanceof \Illuminate\Pagination\LengthAwarePaginator ? ($subRegencies->currentPage() - 1) * $subRegencies->perPage() + $loop->iteration : $index + 1) }}</td>
    <td><span class="badge bg-primary">{{ $subRegency->code }}</span></td>
    <td>{{ $subRegency->name }}</td>
    <td>{{ $subRegency->regency ? $subRegency->regency->name : '-' }}</td>
    <td>{{ $subRegency->regency && $subRegency->regency->province ? $subRegency->regency->province->name : '-' }}</td>
    <td class="text-center">
        <div class="btn-group btn-group-sm" role="group">
            <a href="{{ route('sub-regency.show', $subRegency) }}" class="btn btn-info" title="Detail">
                <i class="bi bi-eye"></i>
            </a>
            <a href="{{ route('sub-regency.edit', $subRegency) }}" class="btn btn-warning" title="Edit">
                <i class="bi bi-pencil"></i>
            </a>
            <form action="{{ route('sub-regency.destroy', $subRegency) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kecamatan ini?')">
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
@if($subRegencies->isEmpty())
<tr>
    <td colspan="6" class="text-center text-muted">Tidak ada data kecamatan</td>
</tr>
@endif
