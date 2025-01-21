@extends('layouts.app')

@section('title', __('Detail of Indikator Persepsi'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Indikator Persepsi') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Detail of indikator Persepsi.') }}
                    </p>
                </div>

                <x-breadcrumb>
                    <li class="breadcrumb-item">
                        <a href="/">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('indikator-persepsi.index') }}">{{ __('Indikator Persepsi') }}</a>
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
                                        <td class="fw-bold">{{ __('Aspek') }}</td>
                                        <td>{{ $indikatorPersepsi->aspek ? $indikatorPersepsi->aspek->aspek : '' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Indikator Persepsi') }}</td>
                                        <td>{{ $indikatorPersepsi->indikator_persepsi }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Kriteria Persepsi') }}</td>
                                        <td>{{ $indikatorPersepsi->kriteria_persepsi }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Created at') }}</td>
                                        <td>{{ $indikatorPersepsi->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Updated at') }}</td>
                                        <td>{{ $indikatorPersepsi->updated_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                </table>
                            </div>

                            <a href="{{ route('indikator-persepsi.index') }}"
                                class="btn btn-secondary"><i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('Back') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
