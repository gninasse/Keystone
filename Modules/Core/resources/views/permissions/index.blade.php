@extends('core::layouts.master')

@section('header', 'Matrice des permissions')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Accueil</a></li>
    <li class="breadcrumb-item active" aria-current="page">Matrice des permissions</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Gestion des privilèges par rôle</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 300px;">Permission</th>
                        @foreach($roles as $role)
                            <th class="text-center">{{ ucfirst($role->name) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $permission)
                        <tr>
                            <td>
                                <strong>{{ $permission->label ?? $permission->name }}</strong>
                                @if($permission->label)
                                    <br><small class="text-muted">{{ $permission->name }}</small>
                                @endif
                            </td>
                            @foreach($roles as $role)
                                <td class="text-center">
                                    <div class="form-check d-inline-block">
                                        <input class="form-check-input permission-toggle" 
                                               type="checkbox" 
                                               data-role-id="{{ $role->id }}" 
                                               data-permission-id="{{ $permission->id }}"
                                               {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@stop

@push('js')
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script type="module" src="{{ asset('js/modules/core/permissions/index.js') }}"></script>
@endpush
