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
        <div class="card-tools">
            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" id="permission-search" class="form-control float-right" placeholder="Rechercher une permission">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-default">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0" id="permissions-table">
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
                        <tr class="permission-row">
                            <td class="permission-name">
                                <strong>{{ $permission->label ?? $permission->name }}</strong>
                                @if($permission->label)
                                    <br><small class="text-muted">{{ $permission->name }}</small>
                                @endif
                            </td>
                            @foreach($roles as $role)
                                <td class="text-center">
                                    <input type="checkbox" 
                                           class="permission-toggle" 
                                           data-role-id="{{ $role->id }}" 
                                           data-permission-id="{{ $permission->id }}"
                                           data-toggle="toggle"
                                           data-on="Oui"
                                           data-off="Non"
                                           data-onstyle="success"
                                           data-offstyle="danger"
                                           data-size="small"
                                           {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
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

@push('css')
<link rel="stylesheet" href="{{ asset('plugins/bootstrap-toggle/css/bootstrap-toggle.css') }}">
<style>
    /* Fix for bootstrap toggle alignment in table */
    .toggle.btn { min-width: 60px; min-height: 30px; }
</style>
@endpush

@push('js')
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap-toggle/js/bootstrap-toggle.js') }}"></script>
<script type="module" src="{{ asset('js/modules/core/permissions/index.js') }}"></script>
@endpush
