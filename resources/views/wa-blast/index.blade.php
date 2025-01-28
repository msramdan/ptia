@extends('layouts.app')

@section('title', __('Wa Blast'))

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-8 order-md-1 order-last">
                    <h3>{{ __('Wa Blast') }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ __('Below is a list of all wa Blast.') }}
                    </p>
                </div>
                <x-breadcrumb>
                    <li class="breadcrumb-item"><a href="/">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Wa Blast') }}</li>
                </x-breadcrumb>
            </div>
        </div>

        <section class="section">
            @can('wa blast create')
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal"
                        data-bs-target="#createWaBlastModal">
                        <i class="fas fa-plus"></i>
                        {{ __('Create a new wa blast') }}
                    </button>
                </div>
            @endcan

            <div class="row">
                {{-- <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            
                        </div>
                    </div>
                </div> --}}

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive p-1">
                                <table class="table table-striped datatables-basic" id="data-table" width="100%">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Session Name') }}</th>
                                            <th>{{ __('Whatsapp Number') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="createWaBlastModal" tabindex="-1" aria-labelledby="createWaBlastModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('wa-blast.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createWaBlastModalLabel">{{ __('Create a new Wa Blast') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="session_name" class="form-label">{{ __('Session Name') }}</label>
                            <input type="text" name="session_name" id="session_name" class="form-control"
                                placeholder="{{ __('Enter session name') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.12.0/datatables.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@push('js')
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.12.0/datatables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            @if (session('success'))
                toastr.success("{{ session('success') }}", "Success", {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-top-right",
                    timeOut: 5000,
                });
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}", "Error", {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-top-right",
                    timeOut: 5000,
                });
            @endif
        });
    </script>
    <script>
        $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('wa-blast.index') }}",
            columns: [{
                    data: 'session_name',
                    name: 'session_name',
                },
                {
                    data: 'whatsapp_number',
                    name: 'whatsapp_number',
                },
                {
                    data: 'status',
                    name: 'status',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
        });
    </script>
@endpush
