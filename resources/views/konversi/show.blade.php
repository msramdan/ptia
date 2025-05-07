@extends('layouts.app')

@section('title', __('Detail of Konversi'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Konversi') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Detail of konversi.') }}
                    </p>
                </div>

                <x-breadcrumb>
                    <li class="breadcrumb-item">
                        <a href="{{route('dashboard')}}">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('konversi.index') }}">{{ __('Konversi') }}</a>
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
                    <td class="fw-bold">{{ __('Jenis Skor') }}</td>
                    <td>{{ $konversi->jenis_skor }}</td>
                </tr>
<tr>
                    <td class="fw-bold">{{ __('Skor') }}</td>
                    <td>{{ $konversi->skor }}</td>
                </tr>
<tr>
                    <td class="fw-bold">{{ __('Konversi') }}</td>
                    <td>{{ $konversi->konversi }}</td>
                </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Created at') }}</td>
                                        <td>{{ $konversi->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Updated at') }}</td>
                                        <td>{{ $konversi->updated_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                </table>
                            </div>

                            <a href="{{ route('konversi.index') }}" class="btn btn-secondary">{{ __('Kembali') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
