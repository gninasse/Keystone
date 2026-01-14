<div class="row mb-3">
    <div class="col-12 text-end">
         <button class="btn btn-primary btn-sm" id="btn-assign-permission">
            <i class="fas fa-plus"></i> Accorder une permission
        </button>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Permission</th>
                <th>Module</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($user->getDirectPermissions() as $permission)
                <tr>
                    <td>{{ $permission->name }}</td>
                    <td><span class="badge bg-secondary">{{ $permission->module ?? 'Global' }}</span></td>
                    <td class="text-center">
                        <button class="btn btn-danger btn-sm remove-permission" data-permission-id="{{ $permission->id }}" data-permission-name="{{ $permission->name }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Aucune permission directe accordée</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
