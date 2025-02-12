<td>
    <button type="button" class="btn btn-success btn-sm set-aktif-btn" data-id="{{ $model->id }}" title="Set Aktif"
        @if ($model->status == 'STOPPED' || $model->is_aktif == 'Yes') disabled @endif>
        <i class="fa fa-check"></i>
    </button>

    <a href="{{ route('wa-blast.show', $model->id) }}" class="btn btn-outline-dark btn-sm" title="Scan Wa">
        <i class="fa-solid fa-qrcode"></i>
    </a>

    @can('wa blast delete')
        <form action="{{ route('wa-blast.destroy', $model->id) }}" method="post" class="d-inline" title="Delete"
            onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
            @csrf
            @method('delete')

            <button class="btn btn-outline-danger btn-sm">
                <i class="ace-icon fa fa-trash-alt"></i>
            </button>
        </form>
    @endcan
</td>
