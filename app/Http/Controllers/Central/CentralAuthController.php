<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CentralAuthController extends Controller
{
    public function create(): View
    {
        return view('central.auth.login', [
            'pageTitle' => 'Superadmin Login | '.config('app.name', 'BukSU Practicum'),
            'loginAction' => route('central.login.store'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('central_superadmin')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'The provided superadmin credentials are invalid.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('central.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('central_superadmin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('central.login');
    }
}
