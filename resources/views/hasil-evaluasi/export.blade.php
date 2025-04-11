<table class="table table-striped" id="data-table" width="100%">
    <thead>
        <tr>
            <th rowspan="2" style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">#
            </th>
            <th rowspan="2" style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">
                {{ __('Evaluator') }}</th>
            <th rowspan="2" style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">
                {{ __('Kode Diklat') }}</th>
            <th rowspan="2" style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">
                {{ __('Nama Diklat') }}</th>
            <th rowspan="2" style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">
                {{ __('Jenis Diklat') }}</th>
            <th colspan="2" style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">
                {{ __('Level 3') }}</th>
            <th colspan="2" style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">
                {{ __('Level 4') }}</th>
        </tr>
        <tr>
            <th style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">Skor</th>
            <th style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">Predikat</th>
            <th style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">Skor</th>
            <th style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">Predikat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($projects as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->user_name }}</td>
                <td>{{ $item->kaldikID }}</td>
                <td>{{ $item->kaldikDesc }}</td>
                <td>{{ $item->nama_diklat_type }}</td>
                <td>{{ $item->avg_skor_level_3 }}</td>
                <td>{{ $item->kriteria_dampak_level_3 }}</td>
                <td>{{ $item->avg_skor_level_4 }}</td>
                <td>{{ $item->kriteria_dampak_level_4 }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
