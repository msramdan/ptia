@extends('layouts.app')

@section('title', __('Detail of Indikator Dampak'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Indikator Dampak') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Detail of indikator dampak.') }}
                    </p>
                </div>

                <x-breadcrumb>
                    <li class="breadcrumb-item">
                        <a href="{{route('dashboard')}}">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('indikator-dampak.index') }}">{{ __('Indikator Dampak') }}</a>
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
                    <td class="fw-bold">{{ __('Nilai Minimal') }}</td>
                    <td>{{ $indikatorDampak->nilai_minimal }}</td>
                </tr>
<tr>
                    <td class="fw-bold">{{ __('Nilai Maksimal') }}</td>
                    <td>{{ $indikatorDampak->nilai_maksimal }}</td>
                </tr>
<tr>
                    <td class="fw-bold">{{ __('Kriteria Dampak') }}</td>
                    <td>{{ $indikatorDampak->kriteria_dampak }}</td>
                </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Created at') }}</td>
                                        <td>{{ $indikatorDampak->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Updated at') }}</td>
                                        <td>{{ $indikatorDampak->updated_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                </table>
                            </div>

                            <a href="{{ route('indikator-dampak.index') }}" class="btn btn-secondary">{{ __('Kembali') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
