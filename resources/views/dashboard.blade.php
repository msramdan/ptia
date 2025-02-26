@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
    <div class="page-heading">
        <h3>Dashboard</h3>
    </div>

    <div class="page-content">
        <section class="row">
            <div class="col-md-12">
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible show fade">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Modal Pengumuman -->
                <div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel"
                    aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="announcementModalLabel">Pengumuman</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @php
                                    $setting = \App\Models\Setting::first();
                                @endphp
                                <p style="text-align: justify">{{ $setting->pengumuman }}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h4>Hi 👋, {{ auth()->user()->name }}</h4>
                        <p>{{ __('You are logged in!') }}</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('show_pengumuman'))
                var announcementModal = new bootstrap.Modal(document.getElementById('announcementModal'));
                announcementModal.show();
                @php
                    session()->forget('show_pengumuman');
                @endphp
            @endif
        });
    </script>
@endpush
