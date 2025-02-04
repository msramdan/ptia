@extends('layouts.app')

@section('title', __('Create Single Sender'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Single Sender') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Tambah data single Sender.') }}
                    </p>
                </div>

                <x-breadcrumb>
                    <li class="breadcrumb-item">
                        <a href="/">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ __('Create Single Sender') }}
                    </li>
                </x-breadcrumb>
            </div>
        </div>

        <section class="section">

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('single-sender.store') }}" method="POST">
                                @csrf
                                @method('POST')

                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="session_name">{{ __('Session Name') }}</label>
                                            <input type="hidden" name="api_key" id="api_key"
                                                class="form-control @error('api_key') is-invalid @enderror"
                                                value="{{ $activeSession->api_key }}" readonly required />
                                            <input type="text" name="session_name" id="session_name"
                                                class="form-control @error('session_name') is-invalid @enderror"
                                                value="{{ $activeSession->session_name }}" readonly required />
                                            @error('session_name')
                                                <span class="text-danger">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="no_wa_pengirim">{{ __('No Wa Pengirim') }}</label>
                                            <input type="text" name="no_wa_pengirim" id="no_wa_pengirim"
                                                class="form-control @error('no_wa_pengirim') is-invalid @enderror"
                                                value="{{ $activeSession->whatsapp_number }}" readonly required />
                                            @error('no_wa_pengirim')
                                                <span class="text-danger">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="no-wa-tujuan">{{ __('No Wa Tujuan') }}</label>
                                            <input type="number" name="no_wa_tujuan" id="no-wa-tujuan"
                                                class="form-control @error('no_wa_tujuan') is-invalid @enderror"
                                                value="{{ isset($singleSender) ? $singleSender->no_wa_tujuan : old('no_wa_tujuan') }}"
                                                placeholder="ex: 62857xxxxxx" required />
                                            @error('no_wa_tujuan')
                                                <span class="text-danger">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="isi-pesan">{{ __('Isi Pesan') }}</label>
                                            <textarea name="isi_pesan" rows="10" id="isi-pesan" class="form-control @error('isi_pesan') is-invalid @enderror"
                                                placeholder="{{ __('Isi Pesan') }}" required>{{ isset($singleSender) ? $singleSender->isi_pesan : old('isi_pesan') }}</textarea>
                                            @error('isi_pesan')
                                                <span class="text-danger">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"
                                        aria-hidden="true"></i> {{ __('Kirim') }}</button>
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
@endpush
