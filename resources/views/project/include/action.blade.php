<td>
    <form action="{{ route('project.updateStatus', $model->id) }}" method="POST" class="form-update-status d-inline">
        @csrf
        @method('put')
        <button type="button" class="btn btn-outline-primary btn-sm btn-update-status" data-id="{{ $model->kaldikID }}"
            title="{{ $model->status === 'Pelaksanaan' ? 'Status sudah Pelaksanaan' : 'Ubah Status Pelaksanaan' }}"
            {{ $model->status === 'Pelaksanaan' ? 'disabled' : '' }}>
            <i class="fas fa-paper-plane"></i>
    </form>

    @can('project delete')
        <form action="{{ route('project.destroy', $model->id) }}" method="post" class="form-delete-project d-inline">
            @csrf
            @method('delete')
            <button type="button" class="btn btn-outline-danger btn-sm btn-delete-project" title="Hapus Data">
                <i class="fas fa-trash-alt"></i>
            </button>
        </form>
    @endcan

    @can('project print')
        <a href="{{ route('project.exportPdf', ['id' => $model->id]) }}" class="btn btn-outline-secondary btn-sm d-inline"
            title="Export PDF" target="_blank">
            <i class="fas fa-print"></i>
        </a>
    @endcan

</td>
