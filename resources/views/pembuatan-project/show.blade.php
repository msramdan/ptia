@extends('layouts.app')

@section('title', __('Detail of Pembuatan Project'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Pembuatan Project') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Detail of pembuatan project.') }}
                    </p>
                </div>

                <x-breadcrumb>
                    <li class="breadcrumb-item">
                        <a href="/">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('pembuatan-project.index') }}">{{ __('Pembuatan Project') }}</a>
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
                                        <td class="fw-bold">{{ __('Jenis Diklat') }}</td>
                                        <td>{{ $pembuatanProject->jenis_diklat }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Kode Diklat') }}</td>
                                        <td>{{ $pembuatanProject->kode_diklat }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Nama Diklat') }}</td>
                                        <td>{{ $pembuatanProject->nama_diklat }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Tanggal Diklat') }}</td>
                                        <td>{{ isset($pembuatanProject->tanggal_diklat) ? $pembuatanProject->tanggal_diklat?->format('Y-m-d H:i:s') : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Created at') }}</td>
                                        <td>{{ $pembuatanProject->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Updated at') }}</td>
                                        <td>{{ $pembuatanProject->updated_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                </table>
                            </div>

                            <a href="{{ route('pembuatan-project.index') }}"
                                class="btn btn-secondary">{{ __('Back') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
