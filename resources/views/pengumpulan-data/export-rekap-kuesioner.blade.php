<table class="table table-bordered table-striped text-center align-middle" id="data-table"
                                    width="100%" style="font-size: 12px">
                                    <thead class="text-center">
                                        <tr>
                                            <th rowspan="3" style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">No.</th>
                                            <th rowspan="3" style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">Nama</th>
                                            <th rowspan="3" style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">NIP</th>
                                            @foreach ($groupedLevels as $level => $aspeks)
                                                <th colspan="{{ count($aspeks) * 4 }}" style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">
                                                    LEVEL {{ $level }}
                                                </th>
                                                <th rowspan="3" style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">
                                                    Total LEVEL {{ $level }} (Data Primer)
                                                </th>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach ($groupedLevels as $level => $aspeks)
                                                @foreach ($aspeks as $aspek)
                                                    <th colspan="4" style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">
                                                        {{ $aspek->aspek }}
                                                    </th>
                                                @endforeach
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach ($groupedLevels as $level => $aspeks)
                                                @foreach ($aspeks as $aspek)
                                                    <th style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">Skor</th>
                                                    <th style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">Konversi</th>
                                                    <th style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle; width: 90px;">Bobot</th>
                                                    <th style="background-color:#D3D3D3; color:#000; font-weight:bold; vertical-align:middle;">Nilai</th>
                                                @endforeach
                                            @endforeach
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($responden as $index => $respondenItem)
                                            @php
                                                $sql = "WITH delta_data AS (
                                            SELECT
                                                project_jawaban_kuesioner.project_responden_id,
                                                project_kuesioner.aspek_id,
                                                project_kuesioner.aspek,
                                                project_kuesioner.kriteria,
                                                project.id AS project_id,
                                                project.diklat_type_id,
                                                project_jawaban_kuesioner.remark,
                                                COUNT(project_jawaban_kuesioner.id) AS jumlah_data,
                                                SUM(project_jawaban_kuesioner.nilai_sesudah - project_jawaban_kuesioner.nilai_sebelum) AS total_delta,
                                                ROUND(AVG(project_jawaban_kuesioner.nilai_sesudah - project_jawaban_kuesioner.nilai_sebelum)) AS rata_rata_delta
                                            FROM project_jawaban_kuesioner
                                            JOIN project_kuesioner ON project_jawaban_kuesioner.project_kuesioner_id = project_kuesioner.id
                                            JOIN project ON project_kuesioner.project_id = project.id
                                            WHERE project_jawaban_kuesioner.project_responden_id = :responden_id
                                                AND project_jawaban_kuesioner.remark = :remark
                                            GROUP BY
                                                project_jawaban_kuesioner.project_responden_id,
                                                project_kuesioner.aspek_id,
                                                project_kuesioner.aspek,
                                                project_kuesioner.kriteria,
                                                project.diklat_type_id,
                                                project_jawaban_kuesioner.remark
                                        )
                                        SELECT
                                            delta_data.*,
                                            COALESCE(konversi.konversi, 0) AS konversi_nilai,
                                            COALESCE(
                                                CASE
                                                    WHEN delta_data.remark = 'Alumni' THEN project_bobot_aspek.bobot_alumni
                                                    WHEN delta_data.remark = 'Atasan' THEN project_bobot_aspek.bobot_atasan_langsung
                                                    ELSE 0
                                                END, 0
                                            ) AS bobot,
                                            -- Perhitungan nilai
                                            ROUND((COALESCE(konversi.konversi, 0) *
                                                   COALESCE(
                                                       CASE
                                                           WHEN delta_data.remark = 'Alumni' THEN project_bobot_aspek.bobot_alumni
                                                           WHEN delta_data.remark = 'Atasan' THEN project_bobot_aspek.bobot_atasan_langsung
                                                           ELSE 0
                                                       END, 0
                                                   ) / 100), 2) AS nilai
                                        FROM delta_data
                                        LEFT JOIN konversi
                                            ON delta_data.diklat_type_id = konversi.diklat_type_id
                                            AND delta_data.rata_rata_delta = konversi.skor
                                            AND (
                                                (delta_data.kriteria = 'Skor Persepsi' AND konversi.jenis_skor = 'Skor Persepsi')
OR
                                                (delta_data.kriteria = 'Delta Skor Persepsi' AND konversi.jenis_skor = '∆ Skor Persepsi')
                                            )
                                        LEFT JOIN project_bobot_aspek
                                            ON delta_data.project_id = project_bobot_aspek.project_id
                                            AND delta_data.aspek_id = project_bobot_aspek.aspek_id
                                            AND delta_data.aspek = project_bobot_aspek.aspek;";

                                                $result = DB::select($sql, [
                                                    'responden_id' => $respondenItem->id,
                                                    'remark' => $remark,
                                                ]);
                                            @endphp

                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $respondenItem->nama }}</td>
                                                <td>{{ $respondenItem->nip }}</td>
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
                                                        <b
                                                            title="Perhitungan: ({{ $data->konversi_nilai ?? 0 }} * {{ $data->bobot ?? 0 }}%)">
                                                            {{ $nilai }}
                                                        </b>
                                                    </td>
                                                @endforeach

                                                {{-- Total Level 3 --}}
                                                <td>
                                                    <b
                                                        title="Total level 3: {{ implode(' + ', collect($groupedLevels[3])->map(fn($aspek) => collect($result)->firstWhere('aspek', $aspek->aspek)->nilai ?? 0)->toArray()) }}">
                                                        {{ $total_level_3 }}
                                                    </b>
                                                </td>


                                                {{-- Looping Level 4 --}}
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
                                                        <b
                                                            title="Perhitungan: ({{ $data->konversi_nilai ?? 0 }} * {{ $data->bobot ?? 0 }}%)">
                                                            {{ $nilai }}
                                                        </b>
                                                    </td>
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
