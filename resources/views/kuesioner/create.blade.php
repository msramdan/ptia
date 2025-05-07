@extends('layouts.app')

@section('title', __('Create Kuesioner'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Kuesioner') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Tambah data kuesioner.') }}
                    </p>
                </div>

                <x-breadcrumb>
                    <li class="breadcrumb-item">
                        <a href="{{route('dashboard')}}">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('kuesioner.index') }}">{{ __('Kuesioner') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ __('Create') }}
                    </li>
                </x-breadcrumb>
            </div>
        </div>

        <section class="section">

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('kuesioner.store') }}" method="POST">
                                @csrf
                                @method('POST')

                                @include('kuesioner.include.form')
                                <a href="{{ route('kuesioner.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('Kembali') }}</a>

                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ __('Save') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
