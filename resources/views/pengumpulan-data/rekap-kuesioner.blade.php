@extends('layouts.app')

@section('title', __('Rekap Kuesioner'))

@section('content')

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Rekap Kuesioner') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Berikut adalah rekap hasil kuesioner.') }}
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="/">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('pengumpulan-data.index') }}">{{ __('Pengumpulan Data') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Rekap Kuesioner') }}</li>
                </x-breadcrumb>
            </div>
        </div>
        <section class="section">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Kode Diklat</strong></td>
                                    <td>: {{ $project->kaldikID ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Diklat</strong></td>
                                    <td>: {{ $project->kaldikDesc ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat Oleh</strong></td>
                                    <td>: {{ $project->user_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Target</strong></td>
                                    <td>:  {{$remark}}</td>
                                </tr>
                            </table>

                            <a href="{{ route('pengumpulan-data.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('kembali') }}
                            </a>
                            <a href="#" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> {{ __('Export Data') }}
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5>Data Rekap Kuesioner</h5>
                            <div class="table-responsive p-1">
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection
