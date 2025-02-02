<td>
    @can('project print')
        <a href="#" class="btn btn-outline-primary btn-sm">
            <i class="fa fa-print"></i>
        </a>
    @endcan

    @can('project delete')
        <form action="{{ route('project.destroy', $model->id) }}" method="post" class="d-inline"
            onsubmit="return confirm('Are you sure to delete this record?')">
            @csrf
            @method('delete')

            <button class="btn btn-outline-danger btn-sm">
                <i class="ace-icon fa fa-trash-alt"></i>
            </button>
        </form>
    @endcan
</td>
