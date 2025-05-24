@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-center min-h-screen bg-gray-100">
        <div class="w-full max-w-md bg-white p-8 rounded shadow">
            <h2 class="text-2xl font-bold mb-6 text-center">Подтверждение регистрации</h2>
            <form method="POST" action="{{ route('register.confirm.submit') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="code">Код подтверждения</label>
                    <input id="code" name="code" type="text" required autofocus
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300"
                           value="{{ old('code') }}" minlength="6" maxlength="6">
                    @error('code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        class="w-full text-white bg-blue-600 py-2 rounded hover:bg-blue-700 dark:hover:bg-blue-800 transition">
                    Подтвердить
                </button>
            </form>
        </div>
    </div>
@endsection
