@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-center bg-gray-100 dark:bg-gray-900 transition-colors">
        <div class="w-full max-w-md bg-white dark:bg-gray-800 p-8 rounded shadow">
            <h2 class="text-2xl font-bold mb-6 text-center text-gray-900 dark:text-gray-100">Регистрация</h2>
            <form method="POST" action="{{ route('register.submit') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 mb-2" for="name">Имя</label>
                    <input id="name" name="name" type="text" required autofocus
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring focus:border-blue-300 dark:focus:border-blue-500"
                           value="{{ old('name') }}">
                    @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 mb-2" for="email">Email</label>
                    <input id="email" name="email" type="email" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring focus:border-blue-300 dark:focus:border-blue-500"
                           value="{{ old('email') }}">
                    @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 mb-2" for="password">Пароль</label>
                    <input id="password" name="password" type="password" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring focus:border-blue-300 dark:focus:border-blue-500">
                    @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 dark:text-gray-300 mb-2" for="password_confirmation">Подтверждение
                        пароля</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring focus:border-blue-300 dark:focus:border-blue-500">
                </div>
                <button type="submit"
                        class="w-full text-white bg-blue-600 py-2 rounded hover:bg-blue-700 dark:hover:bg-blue-800 transition">
                    Зарегистрироваться
                </button>
            </form>
        </div>
    </div>
@endsection
