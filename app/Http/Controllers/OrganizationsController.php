<?php

namespace App\Http\Controllers;

use App\Http\Middleware\RedirectIfCustomer;
use App\Http\Middleware\RedirectIfNotParmitted;
use App\Models\City;
use App\Models\Country;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationsController extends Controller
{
    public function __construct(){
        $this->middleware(RedirectIfNotParmitted::class.':organization');
    }

    public function index(): Response
    {
        return Inertia::render('Organizations/Index', [
            'title' => 'Organizations',
            'filters' => Request::all('search'),
            'organizations' => Organization::orderBy('name')
                ->filter(Request::only('search'))
                ->paginate(8)
                ->withQueryString()
                ->through(function ($organization) {
                    return [
                        'id' => $organization->id,
                        'name' => $organization->name,
                        'phone' => $organization->phone,
                        'city' => $organization->city,
                        'deleted_at' => $organization->deleted_at,
                    ];
                } ),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Organizations/Create',[
            'title' => 'Crear una nueva Empresa',
            'countries' => Country::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'cities' => City::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
        ]);
    }

    public function store(): RedirectResponse
    {
        Organization::create(
            Request::validate([
                'name' => ['required', 'max:100'],
                'email' => ['nullable', 'max:50', 'email'],
                'phone' => ['nullable', 'max:50'],
                'address' => ['nullable', 'max:150'],
                'city' => ['nullable', 'max:50'],
/*                'region' => ['nullable', 'max:50'],
                'country' => ['nullable', 'max:2'],
                'postal_code' => ['nullable', 'max:25'],*/
            ])
        );

        return Redirect::route('organizations')->with('success', 'Empresa Creada.');
    }

    public function edit(Organization $organization): Response
    {
        return Inertia::render('Organizations/Edit', [
            'title' => $organization->name,
            'countries' => Country::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'cities' => City::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'email' => $organization->email,
                'phone' => $organization->phone,
                'address' => $organization->address,
                'city' => $organization->city,
/*                'region' => $organization->region,
                'country' => $organization->country,
                'postal_code' => $organization->postal_code,*/
                'deleted_at' => $organization->deleted_at,
                'users' => $organization->users()->orderByName()->get()->map->only('id', 'first_name','last_name', 'email', 'phone'),
            ],
        ]);
    }

    public function update(Organization $organization): RedirectResponse
    {
        $organization->update(
            Request::validate([
                'name' => ['required', 'max:100'],
                'email' => ['nullable', 'max:50', 'email'],
                'phone' => ['nullable', 'max:50'],
                'address' => ['nullable', 'max:150'],
                'city' => ['nullable', 'max:50'],
/*                'region' => ['nullable', 'max:50'],
                'country' => ['nullable', 'max:2'],
                'postal_code' => ['nullable', 'max:25'],*/
            ])
        );

        return Redirect::back()->with('success', 'Empresa Actualizada');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        $organization->delete();

        return Redirect::route('organizations')->with('success', 'Empresa Eliminada');
    }

    public function restore(Organization $organization): RedirectResponse
    {
        $organization->restore();

        return Redirect::back()->with('success', 'Empresa Restaurada');
    }
}
