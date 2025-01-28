<td>
    <a href="{{ route('wa-blast.show', $model->id) }}" class="btn btn-outline-dark btn-sm">
        <i class="fa-solid fa-qrcode"></i>
    </a>

    
    @can('wa blast delete')
        <form action="{{ route('wa-blast.destroy', $model->id) }}" method="post" class="d-inline"
            onsubmit="return confirm('Are you sure to delete this record?')">
            @csrf
            @method('delete')

            <button class="btn btn-outline-danger btn-sm">
                <i class="ace-icon fa fa-trash-alt"></i>
            </button>
        </form>
    @endcan
</td>
