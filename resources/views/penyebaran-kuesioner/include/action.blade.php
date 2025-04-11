<td>
    @can('project view')
        <a href="{{ route('penyebaran-kuesioner.export-pdf', $model->id) }}" class="btn btn-outline-secondary btn-sm"
            title="Export PDF" target="_blank">
            <i class="fas fa-print"></i>
        </a>
    @endcan
</td>
