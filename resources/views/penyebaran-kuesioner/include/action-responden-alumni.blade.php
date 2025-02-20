<td>

    <a href="#" class="btn btn-warning btn-sm edit-telepon-btn" data-id="{{ $model->id }}"
        data-telepon="{{ $model->telepon }}" data-nama="{{ $model->nama }}" title="Edit No. Telepon">
        <i class="fas fa-phone"></i>
    </a>

    <a href="#" class="btn btn-primary btn-sm send-wa-btn" title="Kirim link Kuesioner ke WhatsApp"
        data-id="{{ $model->id }}" data-remark="Alumni" data-nama="{{ $model->nama }}"
        data-telepon="{{ $model->telepon }}">
        <i class="fas fa-paper-plane"></i>
    </a>

    @php
        $encryptedId = encryptShort($model->id);
        $encryptedTarget = encryptShort('alumni');
        $isBelum = $model->status_pengisian_kuesioner_alumni === 'Belum';
    @endphp

    <a href="{{ route('responden-kuesioner.index', ['id' => $encryptedId, 'target' => $encryptedTarget]) }}"
        class="btn btn-{{ $isBelum ? 'danger' : 'success' }} btn-sm"
        title="{{ $isBelum ? 'Peserta belum melakukan pengisian Kuisioner klik untuk melihat kuesioner' : 'Kuisioner sudah diisi klik untuk melihat data' }}"
        target="_blank">
        <i class="fas fa-clipboard-list" aria-hidden="true"></i>
    </a>
    <a href="#" class="btn btn-secondary btn-sm log-wa-btn" title="Log pengiriman pesan WA"
        data-id="{{ $model->id }}" data-remark="Alumni"  data-nama="{{ $model->nama }}">
        <i class="fa fa-history" aria-hidden="true"></i>
    </a>

</td>
