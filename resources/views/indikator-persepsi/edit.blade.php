@extends('layouts.app')

@section('title', __('Edit Indikator Persepsi'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Indikator Persepsi') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Edit data indikator Persepsi.') }}
                    </p>
                </div>

                <x-breadcrumb>
                    <li class="breadcrumb-item">
                        <a href="{{route('dashboard')}}">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('indikator-persepsi.index') }}">{{ __('Indikator Persepsi') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ __('Edit') }}
                    </li>
                </x-breadcrumb>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('indikator-persepsi.update', $indikatorPersepsi->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                @include('indikator-persepsi.include.form')

                                <a href="{{ route('indikator-persepsi.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('Kembali') }}</a>

                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ __('Update') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
