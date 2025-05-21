<td>

    <a href="#" class="btn btn-warning btn-sm edit-telepon-btn" data-id="{{ $model->id }}"
        data-telepon="{{ $model->telepon_atasan }}" data-nama="{{ $model->nama_atasan }}"
        title="Edit Data Atasan Langsung">
        <i class="fas fa-pencil"></i>
    </a>

    <a href="#" class="btn btn-sm edit-deadline-btn"
        style="background-color: #C0C0C0; color: black; border-color: #C0C0C0;" data-id="{{ $model->id }}"
        data-deadline="{{ $model->deadline_pengisian_atasan }}" data-nama="{{ $model->nama_atasan }}"
        title="Update deadline pengisian kuesioner">
        <i class="fas fa-calendar-alt"></i>
    </a>

    <a href="#" class="btn btn-primary btn-sm send-wa-btn"
        title="Kirim link Kuesioner ke WhatsApp Atasan Langsung" data-id="{{ $model->id }}" data-remark="Atasan"
        data-nama="{{ $model->nama_atasan }}" data-telepon="{{ $model->telepon_atasan }}">
        <i class="fas fa-paper-plane"></i>
    </a>

    @php
        $encryptedId = encryptShort($model->id);
        $encryptedTarget = encryptShort('Atasan');
        $isBelum = $model->status_pengisian_kuesioner_atasan === 'Belum';
        $routeParams = [
            'id' => $encryptedId,
            'target' => $encryptedTarget,
            'token' => $model->token,
        ];

        $baseUrl = env('IS_MASKING', false) ? 'https://registrasi.bpkp.go.id/eptia' : url('/');

        $url = env('IS_MASKING', false)
            ? $baseUrl . route('responden-kuesioner.index', $routeParams, false)
            : route('responden-kuesioner.index', $routeParams);
    @endphp

    <a href="{{ $url }}" class="btn btn-{{ $isBelum ? 'danger' : 'success' }} btn-sm"
        title="{{ $isBelum ? 'Peserta belum melakukan pengisian Kuisioner klik untuk melihat kuesioner' : 'Kuisioner sudah diisi klik untuk melihat data' }}"
        target="_blank">
        <i class="fas fa-clipboard-list" aria-hidden="true"></i>
    </a>
    <a href="#" class="btn btn-secondary btn-sm log-wa-btn" title="Log pengiriman pesan WA"
        data-id="{{ $model->id }}" data-remark="Atasan" data-nama="{{ $model->nama_atasan }}">
        <i class="fa fa-history" aria-hidden="true"></i>
    </a>



</td>
