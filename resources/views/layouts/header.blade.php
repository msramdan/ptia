<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title') - {{ env('APP_NAME') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
        @php
        $settingApp = get_setting();
    @endphp
    @if ($settingApp?->favicon)
        <link rel="shortcut icon" href="{{ asset('storage/uploads/favicons/' . $settingApp->favicon) }}"
            type="image/x-icon">
    @endif
    <link rel="stylesheet" href="{{ asset('mazer') }}/compiled/css/app.css" />
    <link rel="stylesheet" href="{{ asset('mazer') }}/compiled/css/app-dark.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
    integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    @stack('css')
</head>

<body>
    <script src="{{ asset('mazer') }}/static/js/initTheme.js"></script>
    <div id="app">
        @include('layouts.sidebar')
        <div id="main" class="layout-navbar navbar-fixed">
            <header>
                <nav class="navbar navbar-expand navbar-light navbar-top">
                    <div class="container-fluid">
                        <a href="#" class="burger-btn d-block d-lg-none">
                            <i class="bi bi-justify fs-3"></i>
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ms-auto mb-lg-0">
                                <li class="nav-item dropdown me-1">
                                </li>
                            </ul>
                            @auth
                                <div class="dropdown">
                                    <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                        <div class="user-menu d-flex">
                                            <div class="user-name text-end me-3">
                                                <h6 class="mb-0 text-gray-600">{{ auth()?->user()?->name }}</h6>
                                                <p class="mb-0 text-sm text-gray-600">
                                                    {{ isset(auth()?->user()?->roles) ? implode(auth()?->user()?->roles?->map(fn($role) => $role->name)->toArray()) : '-' }}
                                                </p>
                                            </div>
                                            <div class="user-img d-flex align-items-center">
                                                <div class="avatar avatar-md">
                                                    @if (!auth()?->user()?->avatar)
                                                        <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(auth()?->user()?->email))) }}&s=500"
                                                            alt="Avatar">
                                                    @else
                                                        <img src="{{ asset('storage/uploads/avatars/' . auth()?->user()?->avatar) }}"
                                                            alt="Avatar">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton"
                                        style="min-width: 11rem;">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('profile') }}"><i
                                                    class="icon-mid bi bi-person-fill me-2"></i>{{ __('My Profile') }}</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('logout') }}"
                                                onclick="event.preventDefault();document.getElementById('logout-form-nav').submit();">
                                                <i class="bi bi-door-open-fill"></i>
                                                {{ __('Logout') }}
                                            </a>

                                            <form id="logout-form-nav" action="{{ route('logout') }}" method="POST"
                                                class="d-none">
                                                @csrf
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @endauth
                        </div>
                    </div>
                </nav>
            </header>
