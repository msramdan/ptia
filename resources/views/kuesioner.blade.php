<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuesioner Evaluasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Tambahkan Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        .warning-message {
            display: flex;
            align-items: center;
            color: #ffc107;
            /* Warna kuning untuk warning */
            font-size: 14px;
            margin-top: 5px;
        }

        .warning-message i {
            margin-right: 5px;
            /* Jarak ikon dengan teks */
        }
    </style>
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
        <div class="d-flex flex-column align-items-center text-center" style="padding: 15px;">
            <img src="https://registrasi.bpkp.go.id/ptia/assets/logo/Post%20Training%20Impact%20Assesment.png"
                alt="Logo PTIA" class="img-fluid" style="max-height: 80px; width: auto;">

            <p class="fw-bold mt-3" style="color: #284D80;">
                KUESIONER EVALUASI PEMBELAJARAN LEVEL 3 dan 4
            </p>
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
                <!-- Catatan bahwa field bertanda * wajib diisi -->
                <p class="text-danger mb-3"><strong>Catatan:</strong> Kolom dengan tanda <span
                        class="text-danger">*</span> wajib diisi.</p>

                <div class="mb-3">
                    <label class="form-label" style="margin-bottom: 2px;"><strong>Nama Peserta</strong></label>
                    <input type="text" class="form-control" value="{{ $responden->nip }} - {{ $responden->nama }}"
                        readonly style="background-color: #e9ecef;">
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
        <div class="alert alert-warning mb-4" role="alert" style="text-align: justify">
            <strong>Uraian:</strong> Isilah skor dari pernyataan berikut ini menurut persepsi anda dengan melingkari
            skala persepsi 1 - 4. Untuk aspek kemampuan membagikan keilmuan, skor persepsi hanya untuk kondisi setelah
            mengikuti pelatihan. Sedangkan untuk aspek lainnya, skor persepsi terdiri dari kondisi sebelum dan setelah
            mengikuti pelatihan.
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

                    <div class="row">
                        <!-- Sebelum Pelatihan -->
                        <div class="col-md-6 mb-3">
                            <div class="card text-white p-2" style="background-color: #284D80">
                                <strong>Sebelum</strong>
                            </div>
                            <div class="p-3 bg-white">
                                <div class="radio-group">
                                    @for ($i = 1; $i <= 4; $i++)
                                        <label>
                                            <input type="radio" name="sebelum[{{ $item->id }}]"
                                                value="{{ $i }}">
                                            <span>{{ $i }}</span>
                                        </label>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <!-- Sesudah Pelatihan -->
                        <div class="col-md-6 mb-3">
                            <div class="card text-white p-2" style="background-color: #284D80">
                                <strong>Sesudah</strong>
                            </div>
                            <div class="p-3 bg-white">
                                <div class="radio-group">
                                    @for ($i = 1; $i <= 4; $i++)
                                        <label>
                                            <input type="radio" name="sesudah[{{ $item->id }}]"
                                                value="{{ $i }}">
                                            <span>{{ $i }}</span>
                                        </label>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Textarea untuk catatan umum -->
                    <div>
                        <textarea name="catatan[{{ $item->id }}]" class="form-control" rows="3" placeholder="Tambahkan catatan..."
                            style="border: 1px solid #ddd; padding: 10px; border-radius: 5px;"></textarea>

                        <!-- Warning jika skor sebelum dan sesudah sama atau turun -->
                        <div class="warning-message text-danger mt-2" style="display: none;">
                            <strong>WARNING!!!</strong>: data skor sebelum dan sesudah sama atau turun (tidak ada
                            peningkatan).
                        </div>
                    </div>
                </div>
            </div>
        @endforeach


        @if ($responden->status_pengisian_kuesioner_alumni != 'Sudah')
            <div class="d-flex justify-content-center mb-4">
                <button type="submit" class="btn btn-danger" id="submitButton" disabled>
                    <i class="fas fa-paper-plane"></i> Jika Sudah Yakin, Klik untuk kirim data
                </button>
            </div>
        @endif

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
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
                            textarea.removeClass("border-danger"); // Hapus border merah jika ada isi
                        }
                    } else {
                        textarea.removeClass("border-danger").removeAttr("required");
                        warningMessage.hide();
                    }
                });

                let atasanFilled = $("#atasan").val().trim() !== "";
                let noWaFilled = $("#no_wa").val().trim() !== "";

                if (allChecked && allTextareaFilled && atasanFilled && noWaFilled) {
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
                            textarea.removeClass("border-danger"); // Hapus border merah jika ada isi
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

                // Hapus border merah jika textarea sudah terisi
                if (textarea.val().trim() !== "") {
                    textarea.removeClass("border-danger");
                }

                checkFormValidity();
            });

            $("#atasan, #no_wa").on("input", checkFormValidity);

            checkTextareaRequirement();
            checkFormValidity();
        });
    </script>







</body>

</html>
