<?php

namespace App\Http\Controllers;

use App\Http\Middleware\RedirectIfNotAdmin;
use App\Models\Status;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class StatusesController extends Controller
{
    public function __construct(){
        $this->middleware(RedirectIfNotAdmin::class);
    }

    public function index(){
        return Inertia::render('Statuses/Index', [
            'title' => 'Statuses',
            'filters' => Request::all(['search', 'trashed']),
            'statuses' => Status::orderBy('name')
                ->filter(Request::all(['search', 'trashed']))
                ->paginate(10)
                ->withQueryString()
                ->through(function ($priority) {
                    return [
                        'id' => $priority->id,
                        'name' => $priority->name,
                        'slug' => $priority->slug,
                    ];
                } ),
        ]);
    }

    public function create()
    {
        return Inertia::render('Statuses/Create',[
            'title' => 'Crear estados nuevos nuevos',
        ]);
    }

    public function store()
    {
        $request_data = Request::validate([
            'name' => ['required', 'max:50'],
        ]);
        $slug = strtolower(preg_replace('/\s+/', '_', $request_data['name']));

        Status::create([ 'name' => $request_data['name'], 'slug' => $slug ]);

        return Redirect::route('statuses')->with('success', 'Estados creados');
    }

    public function edit(Status $status)
    {
        return Inertia::render('Statuses/Edit', [
            'title' => 'Status',
            'status' => [
                'id' => $status->id,
                'name' => $status->name,
                'slug' => $status->slug,
                'deleted_at' => $status->deleted_at,
            ],
        ]);
    }

    public function update(Status $status)
    {
        $status->update(
            Request::validate([
                'name' => ['required', 'max:50'],
                'slug' => ['required', 'max:50'],
            ])
        );

        return Redirect::back()->with('success', 'Estados actualizados.');
    }

    public function destroy(Status $status){
        $status->delete();
        return Redirect::route('statuses')->with('success', 'Estados eliminados.');
    }

    public function restore(Status $status){
        $status->restore();
        return Redirect::back()->with('success', 'Estados restaurados.');
    }
}
