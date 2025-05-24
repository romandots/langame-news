<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\Users\UserSessionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    public function login(UserSessionService $service, LoginRequest $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            return redirect()->route('home');
        }

        $credentials = $request->getDto();

        if (!$service->login($credentials)) {
            return back()->withErrors(['email' => 'Неверные данные для входа.'])->onlyInput(['email', 'remember']);
        }

        return redirect()->intended(route('home'));
    }

    public function logout(UserSessionService $service): RedirectResponse
    {
        $service->logout();
        return redirect()->route('login');
    }
}
