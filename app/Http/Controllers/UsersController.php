<?php

namespace App\Http\Controllers;

use App\Events\UserCreated;
use App\Http\Middleware\RedirectIfNotAdmin;
use App\Http\Middleware\RedirectIfNotParmitted;
use App\Models\Attachment;
use App\Models\Blog;
use App\Models\City;
use App\Models\Comment;
use App\Models\Country;
use App\Models\Message;
use App\Models\Note;
use App\Models\Participant;
use App\Models\PendingEmail;
use App\Models\PendingUser;
use App\Models\Review;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UsersController extends Controller{
    public function __construct(){
        $this->middleware(RedirectIfNotParmitted::class.':user');
    }
    public function index(): Response
    {
        return Inertia::render('Users/Index', [
            'title' => 'Users',
            'filters' => Request::all(['search','role_id']),
            'roles' => Role::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'users' => User::orderByName()
                ->filter(Request::all(['search','role_id']))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'country' => $user->country_id ? $user->country->name: null,
                    'city' => $user->city,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'photo' => $user->photo_path ?? null,
                    'deleted_at' => $user->deleted_at,
                ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Users/Create',[
            'title' => 'Crear un nuevo usuario',
            'roles' => Role::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'countries' => Country::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'cities' => City::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name')
        ]);
    }

    public function store(): RedirectResponse
    {
        $userRequest = Request::validate([
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'phone' => ['nullable', 'max:25'],
            'email' => ['required', 'max:50', 'email', Rule::unique('users')],
            'password' => ['nullable'],
            'city' => ['nullable'],
            'address' => ['nullable'],
            'country_id' => ['nullable'],
            'role_id' => ['nullable'],
        ]);

        if(Request::file('photo')){
            $userRequest['photo_path'] = '/files/'.Request::file('photo')->store('users', ['disk' => 'file_uploads']);
        }

        $customerRole = Role::where('slug', 'customer')->first();
        if(empty($userRequest['role_id']) && !empty($customerRole)){
            $userRequest['role_id'] = $customerRole->id;
        }

        $user = User::create($userRequest);

        event(new UserCreated(['id' => $user->id, 'password' => $userRequest['password']]));

        return Redirect::route('users')->with('success', 'Usuario creado.');
    }

    public function edit(User $user): Response|RedirectResponse
    {
        $a_user = Auth()->user();

        $roles = Role::pluck('id', 'slug')->all();
        if($a_user['role']['slug'] == 'customer'){
            if($user->id !=$a_user['id']){
                return Redirect::back();
            }
        }elseif($a_user['role']['slug'] == 'manager'){
            if($user->id !=$a_user['id'] && $user->role_id != $roles['customer']??0){
                return Redirect::back();
            }
        }
        return Inertia::render('Users/Edit', [
            'title' => $user->name,
            'roles' => Role::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'role_id' => $user->role_id,
                'city' => $user->city,
                'address' => $user->address,
                'country_id' => $user->country_id,
                'photo' => $user->photo_path ?? null,
                'photo_path' => $user->photo_path ?? null,
                'deleted_at' => $user->deleted_at,
            ],
            'countries' => Country::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
            'cities' => City::orderBy('name')
                ->get()
                ->map
                ->only('id', 'name')
        ]);
    }

    public function update(User $user): RedirectResponse
    {
        if (config('app.demo')) {
            return Redirect::back()->with('error', 'No se permite actualizar el usuario para la demostración en vivo.');
        }

        Request::validate([
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'company' => ['required', 'max:50'],
            'phone' => ['nullable', 'max:25'],
            'email' => ['required', 'max:50', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable'],
            'city' => ['nullable'],
            'address' => ['nullable'],
            'country_id' => ['nullable'],
            'photo' => ['nullable', 'image'],
        ]);

        $user->update(Request::only(['first_name', 'last_name', 'phone', 'email', 'city', 'address', 'country_id','company']));

        if(!empty(Request::get('role_id'))){
            $user->update(['role_id' => Request::get('password')]);
        }

        if(Request::file('photo')){
            if(isset($user->photo_path) && !empty($user->photo_path) && File::exists(public_path($user->photo_path))){
                File::delete(public_path($user->photo_path));
            }
            $user->update(['photo_path' => '/files/'.Request::file('photo')->store('users', ['disk' => 'file_uploads'])]);
        }

        if (Request::get('password')) {
            $user->update(['password' => Request::get('password')]);
        }

        return Redirect::back()->with('success', 'Perfil actualizado.');
    }

    public function destroy(User $user): RedirectResponse
    {

        if (config('app.demo')) {
            return Redirect::back()->with('error', 'No se permite eliminar usuarios para la demostración en vivo.');
        }

        $userId = $user->id;
        $user->delete();
        $this->removeUserFromRelatedTables($userId);

        return Redirect::route('users')->with('success', 'Usuario eliminado!');
    }
    public function restore(User $user): RedirectResponse
    {
        $user->restore();
        return Redirect::back()->with('success', 'Usuario restaurado!');
    }

    private function removeUserFromRelatedTables($userId): void
    {
        Note::where('user_id', $userId)->update(['user_id' => null]);
        PendingEmail::where('user_id', $userId)->update(['user_id' => null]);
        Review::where('user_id', $userId)->update(['user_id' => null]);
        Comment::where('user_id', $userId)->update(['user_id' => null]);
        Attachment::where('user_id', $userId)->update(['user_id' => null]);
        Ticket::where('user_id', $userId)->update(['user_id' => null]);
        Participant::where('user_id', $userId)->update(['user_id' => null]);
        Message::where('user_id', $userId)->update(['user_id' => null]);
        PendingUser::where('user_id', $userId)->update(['user_id' => null]);
    }
}
