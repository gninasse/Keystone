<?php

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display the permissions matrix.
     */
    public function index()
    {
        $roles = Role::orderBy('id')->get();
        // Fetch permissions, perhaps ordered by ID or Name
        $permissions = Permission::orderBy('id')->get();

        return view('core::permissions.index', compact('roles', 'permissions'));
    }

    /**
     * Toggle a permission for a role via AJAX.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id',
            'attach' => 'required|boolean'
        ]);

        try {
            $role = Role::findById($request->role_id);
            $permission = Permission::findById($request->permission_id);

            if ($request->attach) {
                $role->givePermissionTo($permission);
                $message = 'Permission accordée';
            } else {
                $role->revokePermissionTo($permission);
                $message = 'Permission révoquée';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage()
            ], 500);
        }
    }
}
