@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors">
        <div class="w-full max-w-md bg-white dark:bg-gray-800 p-8 rounded shadow">
            <h2 class="text-2xl font-bold text-center text-gray-800">Вход в аккаунт</h2>
            @if ($errors->any())
                <div class="mb-4 text-sm text-red-600">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-gray-700 dark:text-gray-300 mb-2">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300" />
                </div>
                <div>
                    <label for="password" class="block text-gray-700 dark:text-gray-300 mb-2">Пароль</label>
                    <input id="password" name="password" type="password" required
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300" />
                </div>
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="remember" class="ml-2 text-sm text-gray-600">Запомнить меня</label>
                </div>
                <div>
                    <button type="submit"
                            class="w-full text-white bg-blue-600 py-2 rounded hover:bg-blue-700 dark:hover:bg-blue-800 transition">
                        Войти
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
