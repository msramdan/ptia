<td>
    <a href="#" class="btn btn-warning btn-sm" title="Klik untuk merubah nomor Responden Alumni">
        <i class="fa fa-phone"></i>
    </a>
    <a href="#" class="btn btn-primary btn-sm" title="Klik untuk kirim link Kuesioner kepada Responden Alumni">
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

    <a href="#" class="btn btn-secondary btn-sm" title="Log pengiriman pesan WA">
        <i class="fa fa-history" aria-hidden="true"></i>
    </a>
</td>
