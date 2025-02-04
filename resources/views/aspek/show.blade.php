@extends('layouts.app')

@section('title', __('Detail of Aspek'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Aspek') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Detail of aspek.') }}
                    </p>
                </div>

                <x-breadcrumb>
                    <li class="breadcrumb-item">
                        <a href="/">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('aspek.index') }}">{{ __('Aspek') }}</a>
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
                                        <td class="fw-bold">{{ __('Level') }}</td>
                                        <td>{{ $aspek->level }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Aspek') }}</td>
                                        <td>{{ $aspek->aspek }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Urutan') }}</td>
                                        <td>{{ $aspek->urutan }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Created at') }}</td>
                                        <td>{{ $aspek->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">{{ __('Updated at') }}</td>
                                        <td>{{ $aspek->updated_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                </table>
                            </div>

                            <a href="{{ route('aspek.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"
                                    aria-hidden="true"></i> {{ __('Kembali') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
