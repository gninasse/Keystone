<div class="row mb-3">
    <div class="col-12 text-end">
        <button class="btn btn-primary btn-sm" id="btn-assign-role">
            <i class="fas fa-plus"></i> Assigner un rôle
        </button>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Rôle</th>
                <th>Description</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($user->roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->description ?? '-' }}</td>
                    <td class="text-center">
                        <button class="btn btn-danger btn-sm remove-role" data-role-id="{{ $role->id }}" data-role-name="{{ $role->name }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Aucun rôle assigné</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Assign Role Modal would go here or handled by JS/SweetAlert -->
