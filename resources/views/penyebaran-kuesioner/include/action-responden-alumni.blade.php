<td>

    <a href="#" class="btn btn-warning btn-sm edit-telepon-btn" data-id="{{ $model->id }}"
        data-telepon="{{ $model->telepon }}" data-nama="{{ $model->nama }}" title="Edit No. Telepon Alumni">
        <i class="fas fa-phone"></i>
    </a>

    <a href="#" class="btn btn-sm edit-deadline-btn"
        style="background-color: #C0C0C0; color: black; border-color: #C0C0C0;" data-id="{{ $model->id }}"
        data-deadline="{{ $model->deadline_pengisian_alumni }}" data-nama="{{ $model->nama }}"
        title="Update deadline pengisian kuesioner">
        <i class="fas fa-calendar-alt"></i>
    </a>

    <a href="#" class="btn btn-primary btn-sm send-wa-btn" title="Kirim link Kuesioner ke WhatsApp Alumni"
        data-id="{{ $model->id }}" data-remark="Alumni" data-nama="{{ $model->nama }}"
        data-telepon="{{ $model->telepon }}">
        <i class="fas fa-paper-plane"></i>
    </a>

    @php
        $encryptedId = encryptShort($model->id);
        $encryptedTarget = encryptShort('Alumni');
        $isBelum = $model->status_pengisian_kuesioner_alumni === 'Belum';
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
        data-id="{{ $model->id }}" data-remark="Alumni" data-nama="{{ $model->nama }}">
        <i class="fa fa-history" aria-hidden="true"></i>
    </a>

</td>
