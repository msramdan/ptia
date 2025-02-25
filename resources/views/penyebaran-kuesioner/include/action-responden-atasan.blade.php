<td>

    <a href="#" class="btn btn-warning btn-sm edit-telepon-btn" data-id="{{ $model->id }}"
        data-telepon="{{ $model->telepon_atasan }}" data-nama="{{ $model->nama_atasan }}"  title="Edit No. Telepon Atasan Langsung">
        <i class="fas fa-phone"></i>
    </a>

    <a href="#" class="btn btn-primary btn-sm send-wa-btn" title="Kirim link Kuesioner ke WhatsApp Atasan Langsung"
        data-id="{{ $model->id }}" data-remark="Atasan" data-nama="{{ $model->nama_atasan }}"
        data-telepon="{{ $model->telepon_atasan }}">
        <i class="fas fa-paper-plane"></i>
    </a>

    @php
        $encryptedId = encryptShort($model->id);
        $encryptedTarget = encryptShort('Atasan');
        $isBelum = $model->status_pengisian_kuesioner_atasan === 'Belum';
    @endphp

    <a href="{{ route('responden-kuesioner.index', ['id' => $encryptedId, 'target' => $encryptedTarget, 'token' => $model->token]) }}"
        class="btn btn-{{ $isBelum ? 'danger' : 'success' }} btn-sm"
        title="{{ $isBelum ? 'Atasan langsung belum melakukan pengisian Kuisioner klik untuk melihat kuesioner' : 'Kuisioner sudah diisi klik untuk melihat data' }}"
        target="_blank">
        <i class="fas fa-clipboard-list" aria-hidden="true"></i>
    </a>
    <a href="#" class="btn btn-secondary btn-sm log-wa-btn" title="Log pengiriman pesan WA"
        data-id="{{ $model->id }}" data-remark="Atasan"  data-nama="{{ $model->nama_atasan }}">
        <i class="fa fa-history" aria-hidden="true"></i>
    </a>

</td>
