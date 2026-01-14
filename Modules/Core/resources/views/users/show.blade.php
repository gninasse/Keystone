@extends('core::layouts.master')

@section('header', 'Détails de l\'utilisateur : ' . $user->full_name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('cores.users.index') }}">Utilisateurs</a></li>
    <li class="breadcrumb-item active" aria-current="page">Détails</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle"
                         src="{{ $user->avatar_url }}"
                         alt="User profile picture">
                </div>

                <h3 class="profile-username text-center">{{ $user->full_name }}</h3>
                <p class="text-muted text-center">{{ $user->service }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Rôles</b> <a class="float-end">{{ $user->roles->count() }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Permissions directes</b> <a class="float-end">{{ $user->getDirectPermissions()->count() }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Permissions effectives</b> <a class="float-end">{{ $user->getAllPermissions()->count() }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#general" data-bs-toggle="tab">Général</a></li>
                    <li class="nav-item"><a class="nav-link" href="#roles" data-bs-toggle="tab">Rôles</a></li>
                    <li class="nav-item"><a class="nav-link" href="#direct-permissions" data-bs-toggle="tab">Permissions directes</a></li>
                    <li class="nav-item"><a class="nav-link" href="#effective-access" data-bs-toggle="tab">Accès effectifs</a></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="active tab-pane" id="general">
                        @include('core::users.tabs.general')
                    </div>
                    
                    <div class="tab-pane" id="roles">
                        @include('core::users.tabs.roles')
                    </div>
                    
                    <div class="tab-pane" id="direct-permissions">
                        @include('core::users.tabs.direct_permissions')
                    </div>

                    <div class="tab-pane" id="effective-access">
                        @include('core::users.tabs.effective_access')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // Tab persistence
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        localStorage.setItem('activeTab', $(e.target).attr('href'));
    });
    var activeTab = localStorage.getItem('activeTab');
    if(activeTab){
        $('.nav-pills a[href="' + activeTab + '"]').tab('show');
    }
</script>
@endpush
