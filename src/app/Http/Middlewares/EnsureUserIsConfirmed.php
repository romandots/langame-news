<?php

namespace App\Http\Middlewares;

use App\Models\User;
use App\Services\Users\UserRegistrationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsConfirmed
{
    public function handle(Request $request, Closure $next)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user && !$user->is_confirmed) {
            /** @var UserRegistrationService $service */
            $service = app(UserRegistrationService::class);
            $service->sendNewConfirmationCode($user);
            return redirect()->route('register.confirm')->withErrors([
                'code' => 'Ваш аккаунт не подтвержден. Введите код подтверждения.',
            ]);
        }

        return $next($request);
    }
}
