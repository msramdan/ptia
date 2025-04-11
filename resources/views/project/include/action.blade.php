<td>
    {{-- Tombol Update Status --}}
    <form action="{{ route('project.updateStatus', $model->id) }}" method="POST" class="form-update-status d-inline">
        @csrf
        @method('put')
        <button type="button" class="btn btn-outline-primary btn-sm btn-update-status" data-id="{{ $model->kaldikID }}"
            title="{{ $model->status === 'Pelaksanaan' ? 'Status sudah Pelaksanaan' : 'Ubah Status Pelaksanaan' }}"
            {{ $model->status === 'Pelaksanaan' ? 'disabled' : '' }}>
            <i class="fas fa-paper-plane"></i>
    </form>

    {{-- Tombol Export PDF --}}
    {{-- Pastikan user punya permission 'project print' (sesuaikan jika berbeda) --}}
    @can('project print')
        <a href="{{ route('project.exportPdf', ['id' => $model->id]) }}" class="btn btn-outline-info btn-sm d-inline"
            {{-- Gunakan warna lain misal info/biru muda --}} title="Export PDF" target="_blank"> {{-- target="_blank" untuk buka di tab baru --}}
            <i class="fas fa-file-pdf"></i> {{-- Icon PDF --}}
        </a>
    @endcan

    {{-- Tombol Hapus --}}
    @can('project delete')
        <form action="{{ route('project.destroy', $model->id) }}" method="post" class="form-delete-project d-inline">
            @csrf
            @method('delete')
            <button type="button" class="btn btn-outline-danger btn-sm btn-delete-project" title="Hapus Data">
                <i class="fas fa-trash-alt"></i>
            </button>
        </form>
    @endcan
</td>
