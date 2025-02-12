<td>
    <form action="{{ route('project.updateStatus', $model->id) }}" method="POST" class="form-update-status d-inline">
        @csrf
        @method('put')
        <!-- Cek status, jika sudah Pelaksanaan, tombol akan dinonaktifkan -->
        <button type="button" class="btn btn-outline-primary btn-sm btn-update-status"
            data-id="{{ $model->kaldikID }}" title="{{ $model->status === 'Pelaksanaan' ? 'Status sudah Pelaksanaan' : 'Ubah Status Pelaksanaan' }}"
            {{ $model->status === 'Pelaksanaan' ? 'disabled' : '' }}>
            <i class="fas fa-paper-plane"></i>
        </button>
    </form>

    @can('project delete')
        <form action="{{ route('project.destroy', $model->id) }}" method="post" class="form-delete-project d-inline">
            @csrf
            @method('delete')

            <button type="button" class="btn btn-outline-danger btn-sm btn-delete-project"
                title="Hapus Data">
                <i class="fas fa-trash-alt"></i>
            </button>
        </form>
    @endcan
</td>
