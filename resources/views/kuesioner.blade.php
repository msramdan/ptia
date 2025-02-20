<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuesioner Evaluasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #f4f7fc;
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .container {
            max-width: 900px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            background: #ffffff;
        }

        .card-header {
            font-size: 1.0rem;

            background-color: #284D80;
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 10px;
        }

        .card-body {
            padding: 20px;
        }

        .alert {
            border-radius: 8px;
            font-size: 0.95rem;
            background: #fff4e6;
            border-left: 5px solid #ff9800;
        }

        .radio-group {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }

        .radio-group label {
            flex: 1;
            text-align: center;
            background: #e9ecef;
            padding: 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
        }

        .radio-group input[type="radio"] {
            display: none;
        }

        .radio-group input[type="radio"]:checked+label {
            background: #0056b3;
            color: white;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-center align-items-center mb-4">
            <h5 class="fw-bold text-center">KUESIONER EVALUASI PEMBELAJARAN LEVEL 3 dan 4</h5>
        </div>

        <!-- Informasi Diklat -->
        <div class="card mb-4">
            <div class="card-header text-white"><strong>Informasi Diklat</strong></div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-3"><strong>Kode Diklat</strong></div>
                    <div class="col-md-9">{{ $responden->kaldikID }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3"><strong>Nama Diklat</strong></div>
                    <div class="col-md-9">{{ $responden->kaldikDesc }}</div>
                </div>

            </div>
        </div>

        <!-- Form Kuesioner -->
        <div class="card mb-4">
            <div class="card-header text-white"><strong>Form Kuesioner</strong></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" style="margin-bottom: 4px;"><strong>Nama</strong></label>
                    <input type="text" class="form-control" value="{{ $responden->nip }} - {{ $responden->nama }}"
                        readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="margin-bottom: 4px;"><strong>Nama Atasan Langsung</strong></label>
                    <input type="text" class="form-control" value="{{ $responden->nama_atasan ?? '' }}"
                        {{ $responden->status_pengisian_kuesioner_alumni == 'Sudah' ? 'readonly' : '' }}>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="margin-bottom: 4px;"><strong>No. Whatsapp Atasan
                            Langsung</strong></label>
                    <input type="number" class="form-control" placeholder="Contoh pengisian 081234567890"
                        value="{{ $responden->telepon_atasan ?? '' }}"
                        {{ $responden->status_pengisian_kuesioner_alumni == 'Sudah' ? 'readonly' : '' }}>
                </div>
            </div>
        </div>

        <!-- Uraian -->
        <div class="alert alert-warning mb-4" role="alert" style="text-align: justify">
            <strong>Uraian:</strong> Isilah skor dari pernyataan berikut ini menurut persepsi anda dengan melingkari
            skala persepsi 1 - 4. Untuk aspek kemampuan membagikan keilmuan, skor persepsi hanya untuk kondisi setelah
            mengikuti pelatihan. Sedangkan untuk aspek lainnya, skor persepsi terdiri dari kondisi sebelum dan setelah
            mengikuti pelatihan.
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">A. Kemampuan Membagikan Keilmuan</h5>
                <p class="text-muted">1. Sangat tidak setuju <br> 2. Tidak setuju <br> 3. Setuju <br> 4. Sangat setuju
                </p>
                <p class="fw-bold">Setelah mengikuti pelatihan, saya berbagi pengetahuan yang telah saya peroleh selama
                    pelatihan kepada rekan-rekan kerja saya melalui kegiatan pelatihan di kantor sendiri, FGD, sharing
                    session, atau bentuk knowledge sharing lainnya
                    dengan
                    pelatihan ini.</p>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card text-white p-2" style="background-color: #284D80"
                            style="background-color: #284D80">
                            <strong>Sebelum</strong>
                        </div>
                        <div class="p-3 bg-white">
                            <div class="radio-group">
                                <label><input type="radio" name="motivasi_sebelum" value="1"> 1</label>
                                <label><input type="radio" name="motivasi_sebelum" value="2"> 2</label>
                                <label><input type="radio" name="motivasi_sebelum" value="3"> 3</label>
                                <label><input type="radio" name="motivasi_sebelum" value="4"> 4</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card text-white p-2" style="background-color: #284D80">
                            <strong>Sesudah</strong>
                        </div>
                        <div class="p-3 bg-white">
                            <div class="radio-group">
                                <label><input type="radio" name="motivasi_sesudah" value="1"> 1</label>
                                <label><input type="radio" name="motivasi_sesudah" value="2"> 2</label>
                                <label><input type="radio" name="motivasi_sesudah" value="3"> 3</label>
                                <label><input type="radio" name="motivasi_sesudah" value="4"> 4</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">B. Kemampuan Implementasi Keilmuan</h5>
                <p class="text-muted">1. Sangat tidak setuju <br> 2. Tidak setuju <br> 3. Setuju <br> 4. Sangat setuju
                </p>
                <p class="fw-bold">Saya mampu menerapkan ilmu yang telah saya peroleh selama Pelatihan dan Sertifikasi
                    Certified Government Risk Assurer (CGRA) Batch 2 Bagi Pegawai BPKP pada setiap penugasan yang
                    relevan</p>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card text-white p-2" style="background-color: #284D80">
                            <strong>Sebelum</strong>
                        </div>
                        <div class="p-3 bg-white">
                            <div class="radio-group">
                                <label><input type="radio" name="kepercayaan_sebelum" value="1"> 1</label>
                                <label><input type="radio" name="kepercayaan_sebelum" value="2"> 2</label>
                                <label><input type="radio" name="kepercayaan_sebelum" value="3"> 3</label>
                                <label><input type="radio" name="kepercayaan_sebelum" value="4"> 4</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card text-white p-2" style="background-color: #284D80">
                            <strong>Sesudah</strong>
                        </div>
                        <div class="p-3 bg-white">
                            <div class="radio-group">
                                <label><input type="radio" name="kepercayaan_sesudah" value="1"> 1</label>
                                <label><input type="radio" name="kepercayaan_sesudah" value="2"> 2</label>
                                <label><input type="radio" name="kepercayaan_sesudah" value="3"> 3</label>
                                <label><input type="radio" name="kepercayaan_sesudah" value="4"> 4</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">C. Motivasi</h5>
                <p class="text-muted">1. Sangat tidak setuju <br> 2. Tidak setuju <br> 3. Setuju <br> 4. Sangat setuju
                </p>
                <p class="fw-bold">Saya termotivasi untuk terlibat secara aktif dalam setiap penugasan yang relevan
                    dengan pelatihan ini.</p>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card text-white p-2" style="background-color: #284D80">
                            <strong>Sebelum</strong>
                        </div>
                        <div class="p-3 bg-white">
                            <div class="radio-group">
                                <label><input type="radio" name="kepercayaan_sebelum" value="1"> 1</label>
                                <label><input type="radio" name="kepercayaan_sebelum" value="2"> 2</label>
                                <label><input type="radio" name="kepercayaan_sebelum" value="3"> 3</label>
                                <label><input type="radio" name="kepercayaan_sebelum" value="4"> 4</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card text-white p-2" style="background-color: #284D80">
                            <strong>Sesudah</strong>
                        </div>
                        <div class="p-3 bg-white">
                            <div class="radio-group">
                                <label><input type="radio" name="kepercayaan_sesudah" value="1"> 1</label>
                                <label><input type="radio" name="kepercayaan_sesudah" value="2"> 2</label>
                                <label><input type="radio" name="kepercayaan_sesudah" value="3"> 3</label>
                                <label><input type="radio" name="kepercayaan_sesudah" value="4"> 4</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">D. Kepercayaan Diri</h5>
                <p class="text-muted">1. Sangat tidak setuju <br> 2. Tidak setuju <br> 3. Setuju <br> 4. Sangat setuju
                </p>
                <p class="fw-bold">Saya percaya diri untuk terlibat secara aktif dalam setiap kegiatan yang relevan
                    dengan pelatihan ini..</p>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card text-white p-2" style="background-color: #284D80">
                            <strong>Sebelum</strong>
                        </div>
                        <div class="p-3 bg-white">
                            <div class="radio-group">
                                <label><input type="radio" name="kepercayaan_sebelum" value="1"> 1</label>
                                <label><input type="radio" name="kepercayaan_sebelum" value="2"> 2</label>
                                <label><input type="radio" name="kepercayaan_sebelum" value="3"> 3</label>
                                <label><input type="radio" name="kepercayaan_sebelum" value="4"> 4</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card text-white p-2" style="background-color: #284D80">
                            <strong>Sesudah</strong>
                        </div>
                        <div class="p-3 bg-white">
                            <div class="radio-group">
                                <label><input type="radio" name="kepercayaan_sesudah" value="1"> 1</label>
                                <label><input type="radio" name="kepercayaan_sesudah" value="2"> 2</label>
                                <label><input type="radio" name="kepercayaan_sesudah" value="3"> 3</label>
                                <label><input type="radio" name="kepercayaan_sesudah" value="4"> 4</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">E. Hasil Pelatihan</h5>
                <p class="text-muted">1. Sangat tidak setuju <br> 2. Tidak setuju <br> 3. Setuju <br> 4. Sangat setuju
                </p>
                <p class="fw-bold">Implementasi hasil pelatihan ini berdampak positif dalam meningkatkan kualitas
                    manajemen risiko pada organisasi</p>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card text-white p-2" style="background-color: #284D80">
                            <strong>Sebelum</strong>
                        </div>
                        <div class="p-3 bg-white">
                            <div class="radio-group">
                                <label><input type="radio" name="kepercayaan_sebelum" value="1"> 1</label>
                                <label><input type="radio" name="kepercayaan_sebelum" value="2"> 2</label>
                                <label><input type="radio" name="kepercayaan_sebelum" value="3"> 3</label>
                                <label><input type="radio" name="kepercayaan_sebelum" value="4"> 4</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card text-white p-2" style="background-color: #284D80">
                            <strong>Sesudah</strong>
                        </div>
                        <div class="p-3 bg-white">
                            <div class="radio-group">
                                <label><input type="radio" name="kepercayaan_sesudah" value="1"> 1</label>
                                <label><input type="radio" name="kepercayaan_sesudah" value="2"> 2</label>
                                <label><input type="radio" name="kepercayaan_sesudah" value="3"> 3</label>
                                <label><input type="radio" name="kepercayaan_sesudah" value="4"> 4</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($responden->status_pengisian_kuesioner_alumni != 'Sudah')
            <div class="d-flex justify-content-center mb-4">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-paper-plane"></i> Jika Sudah Yakin, Klik untuk kirim data
                </button>
            </div>
        @endif
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
