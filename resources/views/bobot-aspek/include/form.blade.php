<table class="table">
    <thead>
        <tr>
            <th>Aspek</th>
            <th style="width: 160px;">Bobot Alumni</th>
            <th style="width: 160px;">Bobot Atasan</th>
        </tr>
    </thead>
    <tbody>
        <tr style="background: #1a375e; color: #fff;">
            <th colspan="4">LEVEL 3 (Data Primer)</th>
        </tr>
        @foreach ($level3 as $index => $bobot)
            <tr>
                <td>{{ $bobot->aspek_nama ?? 'Tidak Ada Nama' }}</td>
                <td>
                    <div class="input-group ">
                        <input type="number" step="0.01" class="form-control form-control-sm" style="width: 60px;"
                            value="{{ $bobot->bobot_alumni }}" />
                        <span class="input-group-text">%</span>
                    </div>
                </td>
                <td>
                    <div class="input-group ">
                        <input type="number" step="0.01" class="form-control form-control-sm" style="width: 60px;"
                            value="{{ $bobot->bobot_atasan_langsung }}" />
                        <span class="input-group-text">%</span>
                    </div>
                </td>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td style="text-align: center; font-weight: bold;">
                <div class="input-group ">
                    <input type="number" step="0.01" class="form-control form-control-sm" style="width: 60px;" readonly
                        value="100" />
                    <span class="input-group-text">%</span>
                </div>
            </td>
            <td style="text-align: center; font-weight: bold;">
                <div class="input-group ">
                    <input type="number" step="0.01" class="form-control form-control-sm" style="width: 60px;" readonly
                        value="100" />
                    <span class="input-group-text">%</span>
                </div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="background-color: #2e7d32 ; text-align: center; font-weight: bold;" colspan="2">
                <span style="color:#fff;">100 %</span>
            </td>
        </tr>

        {{-- LEVEL 4 --}}
        <tr style="background: #1a375e; color: #fff;">
            <th colspan="4">LEVEL 4 (Data Primer & Sekunder)</th>
        </tr>
        @foreach ($level4 as $index => $bobot)
            <tr>
                <td>Data Primer : {{ $bobot->aspek_nama ?? 'Tidak Ada Nama' }}</td>
                <td>
                    <div class="input-group ">
                        <input type="number" step="0.01" class="form-control form-control-sm" style="width: 60px;"
                            value="{{ $bobot->bobot_alumni }}" />
                        <span class="input-group-text">%</span>
                    </div>
                </td>
                <td>
                    <div class="input-group ">
                        <input type="number" step="0.01" class="form-control form-control-sm" style="width: 60px;"
                            value="{{ $bobot->bobot_atasan_langsung }}" />
                        <span class="input-group-text">%</span>
                    </div>
                </td>
            </tr>
        @endforeach
        <tr>
            <td>Data Sekunder : Hasil Pelatihan</td>
            <td>
                <div class="input-group ">
                    <input type="number" step="0.01" class="form-control form-control-sm" style="width: 60px;"
                        value="12" />
                    <span class="input-group-text">%</span>
                </div>
            </td>
            <td>
                <div class="input-group ">
                    <input type="number" step="0.01" class="form-control form-control-sm" style="width: 60px;"
                        value="12" />
                    <span class="input-group-text">%</span>
                </div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: center; font-weight: bold;">
                <div class="input-group ">
                    <input type="number" step="0.01" class="form-control form-control-sm" style="width: 60px;" readonly
                        value="100" />
                    <span class="input-group-text">%</span>
                </div>
            </td>
            <td style="text-align: center; font-weight: bold;">
                <div class="input-group ">
                    <input type="number" step="0.01" class="form-control form-control-sm" style="width: 60px;" readonly
                        value="100" />
                    <span class="input-group-text">%</span>
                </div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td style="background-color: #c62828 ; text-align: center; font-weight: bold;" colspan="2">
                <span style="color:#fff;">100 %</span>
            </td>
        </tr>
    </tbody>
</table>
