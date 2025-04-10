<td>
    @can('project view')
        {{-- Asumsi minimal perlu bisa lihat project --}}
        <a href="{{ route('penyebaran-kuesioner.export-persiapan-pdf', $model->id) }}" class="btn btn-danger btn-sm"
            title="Export PDF" target="_blank"> {{-- target="_blank" membuka PDF di tab baru --}}
            <i class="fas fa-file-pdf"></i>
        </a>
    @endcan
</td>
