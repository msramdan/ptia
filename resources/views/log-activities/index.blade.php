@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Log Aktivitas</h3>
                    <p class="text-subtitle text-muted">Rekaman semua aktivitas dalam sistem.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Log Aktivitas</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="table1">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Log</th>
                                    <th>User</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($activities as $activity)
                                    <tr>
                                        <td>{{ $activity->created_at->format('d M Y, H:i:s') }}</td>
                                        <td>{{ $activity->description }}</td>
                                        <td>{{ $activity->causer ? $activity->causer->name : 'System' }}</td>
                                        <td>
                                            @php
                                                $ip = $activity->getExtraProperty('ip_address');
                                                $userAgent = $activity->getExtraProperty('user_agent');
                                            @endphp
                                            @if ($ip)
                                                <strong>IP:</strong> {{ $ip }} <br>
                                            @endif
                                            @if ($userAgent)
                                                <small><strong>Agent:</strong>
                                                    {{ \Illuminate\Support\Str::limit($userAgent, 40) }}</small>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada aktivitas yang tercatat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $activities->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
