@extends('layouts.app')

@section('title', __('Edit Pesan WA'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Pesan WA') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Edit data pesan WA.') }}
                    </p>
                </div>

                <x-breadcrumb>
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ __('Pesan Wa') }}
                    </li>
                </x-breadcrumb>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('pesan-wa.update', $pesanWa->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jenis-skor">{{ __('Pesan Ke Alumni') }}</label>
                                            <textarea name="text_pesan_alumni" id="text-pesan-alumni"
                                                class="form-control text-pesan @error('text_pesan_alumni') is-invalid @enderror"
                                                placeholder="{{ __('Text Pesan') }}" required>{{ isset($pesanWa) ? $pesanWa->text_pesan_alumni : old('text_pesan_alumni') }}</textarea>
                                            @error('text_pesan_alumni')
                                                <span class="text-danger">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jenis-skor">{{ __('Pesan Ke Atasan') }}</label>
                                            <textarea name="text_pesan_atasan" id="text-pesan-atasan"
                                                class="form-control text-pesan @error('text_pesan_atasan') is-invalid @enderror"
                                                placeholder="{{ __('Text Pesan') }}" required>{{ isset($pesanWa) ? $pesanWa->text_pesan_atasan : old('text_pesan_atasan') }}</textarea>
                                            @error('text_pesan_atasan')
                                                <span class="text-danger">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                @can('pesan wa edit')
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                                        {{ __('Update') }}</button>
                                @endcan
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>

    <script>
        $(document).ready(function() {
            @if (session('success'))
                toastr.success("{{ session('success') }}", "Success", {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-top-right",
                    timeOut: 5000,
                });
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}", "Error", {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-top-right",
                    timeOut: 5000,
                });
            @endif
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.text-pesan').forEach(textarea => {
                ClassicEditor
                    .create(textarea, {
                        toolbar: [
                            'bold', 'italic', 'link', 'underline', 'strikethrough', '|',
                            'undo', 'redo', '|',
                            'bulletedList', 'numberedList', '|',
                            'blockQuote', '|',
                            'insertTable', 'tableColumn', 'tableRow', 'mergeTableCells'
                        ],
                    })
                    .catch(error => {
                        console.error(error);
                    });
            });
        });
    </script>
@endpush
