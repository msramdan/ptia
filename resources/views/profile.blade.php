@extends('layouts.app')

@section('title', __('Profile'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Profile') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Change your profile information, password and enable/disable two factor authentication.') }}
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="/">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                </x-breadcrumb>
            </div>
        </div>

        <section class="section mt-4">
            <div class="row">
                <div class="col-md-12">

                </div>
            </div>

            {{-- Profile --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('user-profile-information.update') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="email">{{ __('E-mail Address') }}</label>
                                    <input type="email" name="email" readonly
                                        class="form-control @error('email', 'updateProfileInformation') is-invalid @enderror"
                                        id="email" placeholder="{{ __('E-mail Address') }}"
                                        value="{{ old('email') ?? auth()?->user()?->email }}" required>

                                    @error('email', 'updateProfileInformation')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="name">{{ __('Name') }}</label>
                                    <input type="text" name="name" readonly
                                        class="form-control  @error('name', 'updateProfileInformation') is-invalid @enderror"
                                        id="name" placeholder="{{ __('Name') }}"
                                        value="{{ old('name') ?? auth()?->user()?->name }}" required>
                                    @error('name', 'updateProfileInformation')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="avatar avatar-xl mb-3">
                                            @if (!auth()?->user()?->avatar)
                                                <img class="img-thumbnail" src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(auth()?->user()?->email))) }}&s=500">
                                            @else
                                                <img class="img-thumbnail" src="{{ asset('storage/uploads/avatars/' . auth()?->user()?->avatar) }}" style="width: 150px;height: 150px;border-radius: 5%;">
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="avatar">{{ __('Avatar') }}</label>
                                            <input type="file" name="avatar"
                                                class="form-control @error('avatar', 'updateProfileInformation') is-invalid @enderror"
                                                id="avatar">

                                            @error('avatar', 'updateProfileInformation')
                                                <span class="text-danger">
                                                    {{ $message }}
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">{{ __('Update Profile') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
