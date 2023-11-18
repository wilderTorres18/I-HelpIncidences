<?php

namespace App\Http\Controllers;

use App\Http\Middleware\RedirectIfNotAdmin;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;
use Inertia\Response;

class RolesController extends Controller {
    public function __construct(){
        $this->middleware(RedirectIfNotAdmin::class);
    }

    public function create(): Response
    {
        return Inertia::render('Roles/Create',[
            'title' => 'Crear un nuevo rol'
        ]);
    }

    public function index(): Response
    {
        return Inertia::render('Roles/Index', [
            'title' => 'User Roles',
            'filters' => Request::all(['search', 'role_id']),
            'roles' => Role::orderByName()
                ->filter(Request::all(['search']))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                    'updated_at' => $role->updated_at,
                ]),
        ]);
    }

    public function store(): RedirectResponse
    {
        $userRequest = Request::validate([
            'name' => ['required', 'max:50'],
            'slug' => ['required', 'max:50'],
            'access' => ['required'],
        ]);

        $accessData = [];
        foreach ($userRequest['access'] as $ak => $av){
            $accessData[$ak] = [
                'read' => filter_var($av['read'], FILTER_VALIDATE_BOOLEAN),
                'update' => filter_var($av['update'], FILTER_VALIDATE_BOOLEAN),
                'create' => filter_var($av['create'], FILTER_VALIDATE_BOOLEAN),
                'delete' => filter_var($av['delete'], FILTER_VALIDATE_BOOLEAN)
            ];
        }

        Role::create(['access' => json_encode($accessData), 'slug' => $userRequest['slug'], 'name' => $userRequest['name']]);
        return Redirect::route('roles')->with('success', 'Rol Creado.');
    }

    public function edit(Role $role): Response
    {
        return Inertia::render('Roles/Edit', [
            'title' => $role->name,
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
                'access' => $role->access,
                'updated_at' => $role->updated_at,
            ],
        ]);
    }

    public function update(Role $role): RedirectResponse
    {
        if (config('app.demo')) {
            return Redirect::back()->with('error', 'No se permite actualizar el rol para la demostración en vivo.');
        }

        $userRequest = Request::validate([
            'name' => ['required', 'max:50'],
            'slug' => ['required', 'max:50'],
            'access' => ['required'],
        ]);

        $accessData = [];
        foreach ($userRequest['access'] as $ak => $av){
            $accessData[$ak] = [
                'read' => filter_var($av['read'], FILTER_VALIDATE_BOOLEAN),
                'update' => filter_var($av['update'], FILTER_VALIDATE_BOOLEAN),
                'create' => filter_var($av['create'], FILTER_VALIDATE_BOOLEAN),
                'delete' => filter_var($av['delete'], FILTER_VALIDATE_BOOLEAN)
            ];
        }

        $role->update(['access' => $accessData, 'slug' => $userRequest['slug'], 'name' => $userRequest['name']]);
        return Redirect::back()->with('success', 'Rol Actualizado');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if (config('app.demo')) {
            return Redirect::back()->with('error', 'No se permite eliminar roles para la demostración en vivo.');
        }
        $role->delete();
        return Redirect::route('roles')->with('success', '¡El rol ha sido eliminado!');
    }
}
