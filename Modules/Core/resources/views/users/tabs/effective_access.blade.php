<div class="table-responsive">
    <table class="table table-hover table-striped" id="effective-permissions-table">
        <thead>
            <tr>
                <th>Permission</th>
                <th>Source</th>
            </tr>
        </thead>
        <tbody>
            @foreach($user->getAllPermissions() as $permission)
                <tr>
                    <td>{{ $permission->name }}</td>
                    <td>
                        @if($user->hasDirectPermission($permission))
                            <span class="badge bg-primary">Directe</span>
                        @else
                            <span class="badge bg-info">Via Rôle</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#effective-permissions-table').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
             "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
            }
        });
    });
</script>
