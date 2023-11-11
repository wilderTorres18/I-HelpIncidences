<?php

namespace App\Http\Controllers\Auth;

use App\Events\ForgotPassword;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return Response
     */
    public function create(): Response
    {
        $is_demo = (int)config('app.demo');
        return Inertia::render('Auth/Login', ['is_demo' => $is_demo]);
    }

    public function register(): Response
    {
        $is_demo = (int)config('app.demo');
        return Inertia::render('Auth/Register', ['is_demo' => $is_demo]);
    }

    public function forgotPassword(): Response
    {
        $is_demo = (int)config('app.demo');
        return Inertia::render('Auth/ForgotPassword', ['is_demo' => $is_demo]);
    }

    public function forgotPasswordMail(Request $request): RedirectResponse
    {
        $requestData = $request->validate(['email' => 'required|email|exists:users']);

        $token = Str::random(64);
        DB::table('password_resets')->insert([
            'email' => $requestData['email'],
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        event(new ForgotPassword(['email' => $requestData['email'], 'token' => $token]));

        return back()->with('success', 'We have e-mailed your password reset link!');
    }

    public function forgotPasswordToken($token): Response
    {
        return Inertia::render('Auth/ForgotPasswordInput', ['token' => $token]);
    }

    public function forgotPasswordStore(Request $request): RedirectResponse
    {
        $requestData = $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
            'token' => 'required'
        ]);

        $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $requestData['email'],
                'token' => $requestData['token']
            ])
            ->first();

        if(!$updatePassword){
            return Redirect::back()->with('error', 'Invalid email or token!');
        }

        User::where('email', $requestData['email'])->update(['password' => Hash::make($requestData['password'])]);

        DB::table('password_resets')->where(['email'=> $requestData['email']])->delete();

        return Redirect::route('login')->with('success', 'Your password has been changed!');

    }


    /**
     * Handle an incoming authentication request.
     *
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::DASHBOARD);
    }


    public function registerStore(Request $request): RedirectResponse
    {

        $requestData = $request->validate([
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
            'phone' => ['nullable', 'max:20'],
            'company' => ['nullable', 'max:40'],
            'address' => ['nullable'],
        ]);

        $role = Role::where('slug', 'customer')->first();
        if(!empty($role)){
            $requestData['role_id'] = $role->id;
        }else{
            $requestData['role_id'] = 2;
        }


        $user = User::create($requestData);
        Auth::loginUsingId($user->id, true);

        $request->session()->regenerate();
        return redirect()->intended(RouteServiceProvider::DASHBOARD);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
