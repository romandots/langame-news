<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Services\Users\Exceptions\UserRegistrationRedirectWithErrorException;
use App\Services\Users\UserRegistrationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{

    public function showRegistrationForm(): View|RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            return redirect()->route('home');
        }

        return view('auth.register');
    }

    public function submitRegistration(
        UserRegistrationService $service,
        StoreUserRequest $request
    ): RedirectResponse {
        $user = Auth::user();
        if ($user) {
            return redirect()->route('home');
        }

        try {
            $service->registerUser($request->getDto());
            return redirect()->route('register.confirm');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function showConfirmationForm(): View|RedirectResponse
    {
        $user = Auth::user();
        if ($user && $user->is_confirmed) {
            return redirect()->route('home');
        }

        return view('auth.confirm');
    }

    public function submitConfirmation(UserRegistrationService $service, ConfirmUserRequest $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user && $user->is_confirmed) {
            return redirect()->route('home');
        }

        try {
            $service->confirmUser($request->getCode());
            return redirect()->route('home');
        } catch (UserRegistrationRedirectWithErrorException $exception) {
            if ($exception->redirectRoute) {
                return redirect()->route($exception->redirectRoute)->withErrors(['error' => $exception->getMessage()]);
            }
            return redirect()->back()->withErrors(['code' => $exception->getMessage()]);
        }
    }
}
