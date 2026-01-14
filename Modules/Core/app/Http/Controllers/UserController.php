<?php
 namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Core\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Http\Requests\StoreUserRequest;
use Modules\Core\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * Afficher la liste des utilisateurs
     */
    public function index()
    {
        return view('core::users.index');
    }

    /**
     * Récupérer les données pour Bootstrap Table (AJAX)
     */
    public function getData(Request $request)
    {
        $query = User::query();

        // Recherche
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('service', 'like', "%{$search}%");
            });
        }

        // Tri
        $sortBy = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);

        $total = $query->count();
        $users = $query->offset($offset)->limit($limit)->get();

        return response()->json([
            'total' => $total,
            'rows' => $users
        ]);
    }

    /**
     * Récupérer un utilisateur (pour édition)
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            // If request expects JSON (modal edit check), return JSON
            // But now we want a full page for details. 
            // We can keep JSON for flexibility if header present, or just redirect?
            // User requested "change button modify... opening a page details". 
            // So we return a view.

            if (request()->wantsJson()) {
                 return response()->json([
                    'success' => true,
                    'data' => $user
                ]);
            }
            
            return view('core::users.show', compact('user'));

        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé'
                ], 404);
            }
            abort(404);
        }
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function store(StoreUserRequest $request)
    {
        // Validation is handled by StoreUserRequest

        try {
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
            }

            $user = User::create([
                'name' => $request->name,
                'last_name' => $request->last_name,
                'user_name' => $request->user_name,
                'email' => $request->email,
                'service' => $request->service,
                'password' => Hash::make($request->password),
                'avatar' => $avatarPath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur créé avec succès',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            // Validation is handled by UpdateUserRequest

            $user->name = $request->name;
            $user->last_name = $request->last_name;
            $user->user_name = $request->user_name;
            $user->email = $request->email;
            $user->service = $request->service;

            if ($request->hasFile('avatar')) {
                // Delete old avatar
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->avatar = $request->file('avatar')->store('avatars', 'public');
            }

            // Mettre à jour le mot de passe seulement s'il est fourni
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur modifié avec succès',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Empêcher la suppression de son propre compte
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas supprimer votre propre compte'
                ], 403);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword($id)
    {
        try {
            $user = User::findOrFail($id);
            $newPassword = config('core.user_default_password', 'password'); // Default password
            $user->password = Hash::make($newPassword);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe réinitialisé à : ' . $newPassword
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activer/Désactiver un utilisateur
     */
    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas modifier le statut de votre propre compte'
                ], 403);
            }

            $user->is_active = !$user->is_active;
            $user->save();

            $status = $user->is_active ? 'activé' : 'désactivé';

            return response()->json([
                'success' => true,
                'message' => "Utilisateur $status avec succès",
                'is_active' => $user->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage()
            ], 500);
        }
    }
}