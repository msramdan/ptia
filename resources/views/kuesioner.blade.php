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


        .radio-group {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .radio-group {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .radio-group label {
            flex: 1;
            text-align: center;
            background: #e9ecef;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease-in-out, color 0.3s ease-in-out;
            font-weight: bold;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 40px;
            /* Atur tinggi agar konsisten */
        }

        /* Sembunyikan radio button */
        .radio-group input[type="radio"] {
            display: none;
        }

        /* Warna full tanpa padding */
        .radio-group input[type="radio"]:checked+span {
            background: green;
            color: white;
            border-radius: 6px;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        @if ($sudahMengisi)
            <div class="alert alert-success d-flex align-items-center" role="alert"
                style="border-radius: 8px;
        font-size: 0.95rem;
        background: #e6fff4; /* Hijau muda */
        border-left: 5px solid #28a745;">
                <!-- Hijau tua -->
                <i class="fas fa-check-circle me-2 fa-lg" style="color: #28a745;"></i>
                <div>
                    Terima kasih! Anda telah mengisi kuesioner ini. Jawaban Anda sangat berarti bagi kami.
                </div>
            </div>
        @else
            @if ($isExpired)
                <div class="alert alert-danger d-flex align-items-center" role="alert"
                    style="border-radius: 8px;
            font-size: 0.95rem;
            background: #ffe6e6; /* Merah muda */
            border-left: 5px solid #dc3545;">
                    <!-- Merah tua -->
                    <i class="fas fa-exclamation-circle me-2 fa-lg" style="color: #dc3545;"></i>
                    <div>
                        Form kuesioner sudah ditutup karena melewati batas waktu pengisian. Jika ada pertanyaan, silakan
                        hubungi administrator.
                    </div>
                </div>
            @endif
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="d-flex flex-column align-items-center text-center" style="padding: 15px;">
            <img src="https://registrasi.bpkp.go.id/ptia/assets/logo/Post%20Training%20Impact%20Assesment.png"
                alt="Logo PTIA" class="img-fluid" style="max-height: 80px; width: auto;">

            <p class="fw-bold mt-3" style="color: #284D80;">
                KUESIONER EVALUASI PEMBELAJARAN LEVEL 3 dan 4
            </p>
        </div>

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

        <form action="{{ route('responden-kuesioner.store') }}" method="POST"
            @if ($sudahMengisi || $isExpired) style="pointer-events: none; opacity: 0.6;" @endif>
            @csrf
            <!-- Form Kuesioner -->
            <div class="card mb-4">
                <div class="card-header text-white"><strong>Form Kuesioner</strong></div>
                <div class="card-body">
                    <!-- Catatan bahwa field bertanda * wajib diisi -->
                    <p class="text-danger mb-3"><strong>Catatan:</strong> Kolom dengan tanda <span
                            class="text-danger">*</span> wajib diisi.</p>
                    <input type="hidden" id="remark" name="remark" class="form-control" value="{{ $target }}"
                        readonly style="background-color: #e9ecef;">
                    <input type="hidden" id="project_responden_id" name="project_responden_id" class="form-control"
                        value="{{ $responden->id }}" readonly style="background-color: #e9ecef;">
                    <input type="hidden" id="project_id" name="project_id" class="form-control"
                        value="{{ $responden->project_id }}" readonly style="background-color: #e9ecef;">

                    <div class="mb-3">
                        <label class="form-label" style="margin-bottom: 2px;"><strong>Nama Peserta</strong></label>
                        <input type="text" class="form-control"
                            value="{{ $responden->nip }} - {{ $responden->nama }}" readonly
                            style="background-color: #e9ecef;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="margin-bottom: 2px;">
                            <strong>Nama Atasan Langsung</strong> <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="atasan" name="atasan"
                            value="{{ $responden->nama_atasan ?? '' }}"
                            {{ $target == 'Atasan' || $responden->status_pengisian_kuesioner_alumni == 'Sudah' ? 'readonly style=background-color:#e9ecef;' : '' }}>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="margin-bottom: 2px;">
                            <strong>No. Whatsapp Atasan Langsung</strong> <span class="text-danger">*</span>
                        </label>
                        <input type="number" id="no_wa" name="no_wa" class="form-control"
                            placeholder="Contoh. 081234567890" value="{{ $responden->telepon_atasan ?? '' }}"
                            {{ $target == 'Atasan' || $responden->status_pengisian_kuesioner_alumni == 'Sudah' ? 'readonly style=background-color:#e9ecef;' : '' }}>
                    </div>
                </div>
            </div>

            <!-- Uraian -->
            @php
                $aspekSkorPersepsi = $kuesioner
                    ->where('kriteria', 'Skor Persepsi')
                    ->pluck('aspek')
                    ->unique()
                    ->implode(', ');
            @endphp

            <div class="alert alert-warning mb-4" role="alert"
                style="text-align: justify; border-radius: 8px;
            font-size: 0.95rem;
            background: #fff4e6;
            border-left: 5px solid #ff9800;">
                <strong>Uraian:</strong> Isilah skor untuk setiap pernyataan berikut berdasarkan persepsi Anda dengan
                memilih skala 1 - 4.
                @if ($aspekSkorPersepsi)
                    Untuk aspek <strong>{{ $aspekSkorPersepsi }}</strong>, hanya perlu mengisi skor persepsi
                    <strong>setelah mengikuti pelatihan</strong>.
                @endif
                Untuk aspek lainnya, isi skor persepsi <strong>baik sebelum maupun setelah pelatihan</strong>.
            </div>


            @foreach ($kuesioner as $item)
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">{{ $loop->iteration }}. {{ $item->aspek }}</h5>
                        <p class="text-muted">
                            1. Sangat tidak setuju <br>
                            2. Tidak setuju <br>
                            3. Setuju <br>
                            4. Sangat setuju
                        </p>
                        <p class="fw-bold">{{ $item->pertanyaan }}</p>

                        <input type="hidden" name="project_kuesioner_id[{{ $item->id }}]"
                            value="{{ $item->id }}">
                        <input type="hidden" name="aspek_id[{{ $item->id }}]" value="{{ $item->aspek_id }}">
                        <input type="hidden" name="kriteria[{{ $item->id }}]" value="{{ $item->kriteria }}">
                        <input type="hidden" name="level[{{ $item->id }}]" value="{{ $item->level }}">

                        <div class="row">
                            @if ($item->kriteria === 'Delta Skor Persepsi')
                                <!-- Sebelum Pelatihan (Hanya Jika Kriteria adalah Delta Skor Persepsi) -->
                                <div class="col-md-6 mb-3">
                                    <div class="card text-white p-2" style="background-color: #284D80">
                                        <strong>Sebelum</strong>
                                    </div>
                                    <div class="p-3 bg-white">
                                        <div class="radio-group">
                                            @for ($i = 1; $i <= 4; $i++)
                                                <label>
                                                    <input type="radio" name="sebelum[{{ $item->id }}]"
                                                        value="{{ $i }}"
                                                        {{ $item->nilai_sebelum == $i ? 'checked' : '' }}>
                                                    <span>{{ $i }}</span>
                                                </label>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Sesudah Pelatihan -->
                            <div class="col-md-{{ $item->kriteria === 'Delta Skor Persepsi' ? '6' : '12' }} mb-3">
                                <div class="card text-white p-2" style="background-color: #284D80">
                                    <strong>Sesudah</strong>
                                </div>
                                <div class="p-3 bg-white">
                                    <div class="radio-group">
                                        @for ($i = 1; $i <= 4; $i++)
                                            <label>
                                                <input type="radio" name="sesudah[{{ $item->id }}]"
                                                    value="{{ $i }}"
                                                    {{ $item->nilai_sesudah == $i ? 'checked' : '' }}>
                                                <span>{{ $i }}</span>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Textarea untuk catatan -->
                        <div>
                            <textarea name="catatan[{{ $item->id }}]" class="form-control" rows="3"
                                placeholder="Tambahkan catatan..." style="border: 1px solid #ddd; padding: 10px; border-radius: 5px;">{{ $item->catatan }}</textarea>

                            <!-- Warning jika skor sebelum dan sesudah sama atau turun -->
                            <div class="warning-message text-danger mt-2" style="display: none;">
                                <strong>WARNING!!!</strong>: data skor sebelum dan sesudah sama atau turun (tidak ada
                                peningkatan).
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach



            @if (!$sudahMengisi && !$isExpired)
                <div class="d-flex justify-content-center mb-4">
                    <button type="submit" class="btn btn-danger" id="submitButton">
                        <i class="fas fa-paper-plane"></i> Jika Sudah Yakin, Klik untuk Kirim Data
                    </button>
                </div>
            @endif


        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function cleanNumber(value) {
                return value.replace(/[^0-9]/g, ''); // Hanya menyisakan angka
            }

            function validateNoWa() {
                let noWa = $('#no_wa').val();
                let regex = /^(0|62)\d{9,13}$/;
                let errorMsg = $('#no_wa_error');
                let isValid = regex.test(noWa); // Cek validasi

                if (!isValid) {
                    if (errorMsg.length === 0) {
                        $('#no_wa').after(
                            '<small id="no_wa_error" class="text-danger">Nomor WhatsApp harus diawali dengan 0 atau 62 dan memiliki panjang 10-15 digit.</small>'
                        );
                    }
                } else {
                    errorMsg.remove();
                }

                checkFormValidity(); // Pastikan form dicek ulang
            }

            function checkFormValidity() {
                let allChecked = true;
                let allTextareaFilled = true;

                $(".radio-group").each(function() {
                    if (!$(this).find("input[type='radio']:checked").length) {
                        allChecked = false;
                    }
                });

                $(".card").each(function() {
                    let selectedValueSesudah = $(this).find("input[name^='sesudah']:checked").val();
                    let beforeValue = $(this).find("input[name^='sebelum']:checked").val();
                    let textarea = $(this).find("textarea");
                    let warningMessage = $(this).find(".warning-message");

                    let requiresTextarea = false;
                    let warningText = "";

                    if (selectedValueSesudah == "1" || selectedValueSesudah == "2") {
                        requiresTextarea = true;
                        warningText =
                            '<i class="fas fa-exclamation-triangle fa-2x"></i> Anda memberikan skor 1 atau 2 pada periode sesudah pelatihan. Jelaskan alasannya.';
                    }

                    if (beforeValue && selectedValueSesudah && parseInt(selectedValueSesudah) <= parseInt(
                            beforeValue)) {
                        requiresTextarea = true;
                        if (warningText) {
                            warningText =
                                '<i class="fas fa-exclamation-triangle fa-2x"></i> Anda memberikan skor 1 atau 2 pada periode sesudah pelatihan, dan nilai sesudah sama atau lebih rendah dari sebelum pelatihan. Jelaskan alasannya.';
                        } else {
                            warningText =
                                '<i class="fas fa-exclamation-triangle fa-2x"></i> Anda memberikan skor sesudah sama atau lebih rendah dari sebelum pelatihan. Jelaskan alasannya.';
                        }
                    }

                    if (requiresTextarea) {
                        if (textarea.val().trim() === "") {
                            textarea.addClass("border-danger").attr("required", true);
                            warningMessage.html(warningText).show();
                            allTextareaFilled = false;
                        } else {
                            textarea.removeClass("border-danger");
                        }
                    } else {
                        textarea.removeClass("border-danger").removeAttr("required");
                        warningMessage.hide();
                    }
                });

                let atasanFilled = $("#atasan").val().trim() !== "";
                let noWa = $("#no_wa").val().trim();
                let noWaValid = /^(0|62)\d{9,13}$/.test(noWa); // Validasi nomor WA

                if (allChecked && allTextareaFilled && atasanFilled && noWaValid) {
                    $("#submitButton").prop("disabled", false);
                    $("#alertMessage").hide();
                } else {
                    $("#submitButton").prop("disabled", true);
                    $("#alertMessage").show();
                }
            }

            function checkTextareaRequirement() {
                $(".card").each(function() {
                    let selectedValueSesudah = $(this).find("input[name^='sesudah']:checked").val();
                    let beforeValue = $(this).find("input[name^='sebelum']:checked").val();
                    let textarea = $(this).find("textarea");
                    let warningMessage = $(this).find(".warning-message");

                    let requiresTextarea = false;
                    let warningText = "";

                    if (selectedValueSesudah == "1" || selectedValueSesudah == "2") {
                        requiresTextarea = true;
                        warningText =
                            '<i class="fas fa-exclamation-triangle fa-2x"></i> Anda memberikan skor 1 atau 2 pada periode sesudah pelatihan. Jelaskan alasannya.';
                    }

                    if (beforeValue && selectedValueSesudah && parseInt(selectedValueSesudah) <= parseInt(
                            beforeValue)) {
                        requiresTextarea = true;
                        if (warningText) {
                            warningText =
                                '<i class="fas fa-exclamation-triangle fa-2x"></i> Anda memberikan skor 1 atau 2 pada periode sesudah pelatihan, dan nilai sesudah sama atau lebih rendah dari sebelum pelatihan. Jelaskan alasannya.';
                        } else {
                            warningText =
                                '<i class="fas fa-exclamation-triangle fa-2x"></i> Anda memberikan skor sesudah sama atau lebih rendah dari sebelum pelatihan. Jelaskan alasannya.';
                        }
                    }

                    if (requiresTextarea) {
                        if (textarea.val().trim() === "") {
                            textarea.addClass("border-danger").attr("required", true);
                            warningMessage.html(warningText).show();
                        } else {
                            textarea.removeClass("border-danger");
                        }
                    } else {
                        textarea.removeClass("border-danger").removeAttr("required");
                        warningMessage.hide();
                    }
                });

                checkFormValidity();
            }

            $("input[name^='sesudah'], input[name^='sebelum']").on("change", function() {
                checkTextareaRequirement();
            });

            $("textarea").on("input", function() {
                let textarea = $(this);
                if (textarea.val().trim() !== "") {
                    textarea.removeClass("border-danger");
                }
                checkFormValidity();
            });

            $("#atasan, #no_wa").on("input", checkFormValidity);

            $("#no_wa").on("input", function() {
                let noWa = cleanNumber($(this).val());
                $(this).val(noWa);
                validateNoWa();
            });

            $("#no_wa").on("paste", function(e) {
                e.preventDefault();
                let pastedData = (e.originalEvent || e).clipboardData.getData('text');
                let cleanedData = cleanNumber(pastedData);
                $(this).val(cleanedData);
                validateNoWa();
            });

            $("#no_wa").on("blur", function() {
                validateNoWa();
            });

            // Cek validasi saat halaman dimuat
            checkTextareaRequirement();
            checkFormValidity();
            validateNoWa();
        });
    </script>



</body>

</html>
