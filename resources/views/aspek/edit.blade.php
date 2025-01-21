@extends('layouts.app')

@section('title', __('Edit Aspek'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Aspek') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Edit a aspek.') }}
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
                            <form action="{{ route('aspek.update', $aspek->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                @include('aspek.include.form')

                                <a href="{{ route('aspek.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left" aria-hidden="true"></i> {{ __('Back') }}</a>

                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ __('Update') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
