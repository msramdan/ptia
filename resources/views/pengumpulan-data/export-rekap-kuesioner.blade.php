<table class="table table-bordered table-striped text-center align-middle" id="data-table" width="100%"
    style="font-size: 12px">
    <thead class="text-center">
        <tr>
            <th rowspan="3" style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">No.
            </th>
            <th rowspan="3" style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">Nama
                Peserta</th>
            <th rowspan="3" style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">NIP
            </th>
            @if ($remark == 'Atasan')
                <th rowspan="3"
                    style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">Nama Atlas
                </th>
            @endif

            @foreach ($groupedLevels as $level => $aspeks)
                {{-- MODIFIKASI: colspan diubah dari count * 4 menjadi count * 5 --}}
                <th colspan="{{ count($aspeks) * 5 }}"
                    style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">
                    LEVEL {{ $level }}
                </th>
                <th rowspan="3"
                    style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">
                    Total LEVEL {{ $level }} (Data Primer)
                </th>
            @endforeach
        </tr>
        <tr>
            @foreach ($groupedLevels as $level => $aspeks)
                @foreach ($aspeks as $aspek)
                    {{-- MODIFIKASI: colspan diubah dari 4 menjadi 5 --}}
                    <th colspan="5"
                        style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">
                        {{ $aspek->aspek }}
                    </th>
                @endforeach
            @endforeach
        </tr>
        <tr>
            @foreach ($groupedLevels as $level => $aspeks)
                @foreach ($aspeks as $aspek)
                    <th style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">Skor</th>
                    <th style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">Konversi
                    </th>
                    <th
                        style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle; width: 90px;">
                        Bobot</th>
                    <th style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">Nilai
                    </th>
                    {{-- PENAMBAHAN: Header kolom Catatan --}}
                    <th style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">Catatan
                    </th>
                @endforeach
            @endforeach
        </tr>
    </thead>

    <tbody>
        @foreach ($responden as $index => $respondenItem)
            @php
                // MODIFIKASI: Query SQL diubah untuk mengambil catatan_aspek
                $sql = "WITH delta_data AS (
                                            SELECT
                                                pjk.project_responden_id,
                                                pk.aspek_id,
                                                pk.aspek,
                                                pk.kriteria,
                                                p.id AS project_id,
                                                p.diklat_type_id,
                                                pjk.remark,
                                                COUNT(pjk.id) AS jumlah_data,
                                                SUM(pjk.nilai_sesudah - pjk.nilai_sebelum) AS total_delta,
                                                ROUND(AVG(pjk.nilai_sesudah - pjk.nilai_sebelum)) AS rata_rata_delta
                                            FROM project_jawaban_kuesioner pjk
                                            JOIN project_kuesioner pk ON pjk.project_kuesioner_id = pk.id
                                            JOIN project p ON pk.project_id = p.id
                                            WHERE pjk.project_responden_id = :responden_id_sql_param
                                                AND pjk.remark = :remark_sql_param
                                            GROUP BY
                                                pjk.project_responden_id,
                                                pk.aspek_id,
                                                pk.aspek,
                                                pk.kriteria,
                                                p.id, -- Tambahkan p.id ke GROUP BY
                                                p.diklat_type_id,
                                                pjk.remark
                                        )
                                        SELECT
                                            dd.*,
                                            (SELECT GROUP_CONCAT(pjk_inner.catatan SEPARATOR '; ')
                                                FROM project_jawaban_kuesioner pjk_inner
                                                JOIN project_kuesioner pk_inner_q ON pjk_inner.project_kuesioner_id = pk_inner_q.id
                                                WHERE pjk_inner.project_responden_id = dd.project_responden_id
                                                AND pk_inner_q.aspek_id = dd.aspek_id
                                                AND pjk_inner.remark = dd.remark
                                                AND pjk_inner.catatan IS NOT NULL AND pjk_inner.catatan != '')
AS catatan_aspek,
                                            COALESCE(k.konversi, 0) AS konversi_nilai,
                                            COALESCE(
                                                CASE
                                                    WHEN dd.remark = 'Alumni' THEN pba.bobot_alumni
                                                    WHEN dd.remark = 'Atasan' THEN pba.bobot_atasan_langsung
                                                    ELSE 0
                                                END, 0
                                            ) AS bobot,
                                            ROUND((COALESCE(k.konversi, 0) *
                                                   COALESCE(
                                                       CASE
                                                           WHEN dd.remark = 'Alumni' THEN pba.bobot_alumni
                                                           WHEN dd.remark = 'Atasan' THEN pba.bobot_atasan_langsung
                                                           ELSE 0
                                                       END, 0
                                                   ) / 100), 2) AS nilai
                                        FROM delta_data dd
                                        LEFT JOIN konversi k
                                            ON dd.diklat_type_id = k.diklat_type_id
                                            AND dd.rata_rata_delta = k.skor
                                            AND (
                                                (dd.kriteria = 'Skor Persepsi' AND k.jenis_skor = 'Skor Persepsi')
OR
                                                (dd.kriteria = 'Delta Skor Persepsi' AND k.jenis_skor = '∆ Skor Persepsi')
                                            )
                                        LEFT JOIN project_bobot_aspek pba
                                            ON dd.project_id = pba.project_id
                                            AND dd.aspek_id = pba.aspek_id
                                            AND dd.aspek = pba.aspek";

                $result = DB::select($sql, [
                    'responden_id_sql_param' => $respondenItem->id, // Ganti nama parameter binding
                    'remark_sql_param' => $remark, // Ganti nama parameter binding
                ]);
            @endphp

            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $respondenItem->nama }}</td>
                <td>{{ $respondenItem->nip }}</td>
                @if ($remark == 'Atasan')
                    <td>{{ $respondenItem->nama_atasan }}</td>
                @endif

                @php
                    $total_level_3 = 0;
                @endphp
                @foreach ($groupedLevels[3] as $aspek)
                    @php
                        $data = collect($result)->firstWhere('aspek', $aspek->aspek);
                        $nilai = $data->nilai ?? 0;
                        $total_level_3 += $nilai;
                    @endphp
                    <td>{{ $data->rata_rata_delta ?? '-' }}</td>
                    <td>{{ $data->konversi_nilai ?? '-' }}</td>
                    <td>{{ $data->bobot ?? '-' }}%</td>
                    <td>
                        <b title="Perhitungan: ({{ $data->konversi_nilai ?? 0 }} * {{ $data->bobot ?? 0 }}%)">
                            {{ $nilai }}
                        </b>
                    </td>
                    {{-- PENAMBAHAN: Sel untuk menampilkan catatan_aspek --}}
                    <td>{{ $data->catatan_aspek ?? '-' }}</td>
                @endforeach

                <td>
                    <b
                        title="Total level 3: {{ implode(' + ', collect($groupedLevels[3])->map(fn($aspek) => collect($result)->firstWhere('aspek', $aspek->aspek)->nilai ?? 0)->toArray()) }}">
                        {{ $total_level_3 }}
                    </b>
                </td>

                @php
                    $total_level_4 = 0;
                @endphp
                @foreach ($groupedLevels[4] as $aspek)
                    @php
                        $data = collect($result)->firstWhere('aspek', $aspek->aspek);
                        $nilai = $data->nilai ?? 0;
                        $total_level_4 += $nilai;
                    @endphp
                    <td>{{ $data->rata_rata_delta ?? '-' }}</td>
                    <td>{{ $data->konversi_nilai ?? '-' }}</td>
                    <td>{{ $data->bobot ?? '-' }}%</td>
                    <td>
                        <b title="Perhitungan: ({{ $data->konversi_nilai ?? 0 }} * {{ $data->bobot ?? 0 }}%)">
                            {{ $nilai }}
                        </b>
                    </td>
                    {{-- PENAMBAHAN: Sel untuk menampilkan catatan_aspek --}}
                    <td>{{ $data->catatan_aspek ?? '-' }}</td>
                @endforeach
                <td>
                    <b
                        title="Total level 4: {{ implode(' + ', collect($groupedLevels[4])->map(fn($aspek) => collect($result)->firstWhere('aspek', $aspek->aspek)->nilai ?? 0)->toArray()) }}">
                        {{ $total_level_4 }}
                    </b>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
