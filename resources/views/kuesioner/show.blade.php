@extends('layouts.app')

@section('title', __('Detail of Kuesioner'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Kuesioner') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Detail of kuesioner.') }}
                    </p>
                </div>

                <x-breadcrumb>
                    <li class="breadcrumb-item">
                        <a href="/">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('kuesioner.index') }}">{{ __('Kuesioner') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ __('Detail') }}
                    </li>
                </x-breadcrumb>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <tr>
                                        <td class="fw-bold">{{ __('Diklat Type') }}</td>
                                        <td>{{ $kuesioner->nama_diklat_type }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Aspek') }}</td>
                                        <td>{{ $kuesioner->aspek }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Pertanyaan') }}</td>
                                        <td>{{ $kuesioner->pertanyaan }}</td>
                                    </tr>
                                </table>
                            </div>
                            <a href="{{ route('kuesioner.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"
                                aria-hidden="true"></i> {{ __('Kembali') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
