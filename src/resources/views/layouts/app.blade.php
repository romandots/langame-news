<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Langame News</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body
    class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
<header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">
    <nav class="flex items-center justify-end gap-4">
        @auth
            <a
                href="{{ route('home') }}"
                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal
                    {{ Route::currentRouteName() === 'home' ? 'bg-gray-200 dark:bg-gray-800 font-semibold' : '' }}"
            >
                Новости
            </a>
            <a
                href="{{ route('users') }}"
                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal
                    {{ Route::currentRouteName() === 'users' ? 'bg-gray-200 dark:bg-gray-800 font-semibold' : '' }}"
            >
                Пользователи
            </a>
            <a
                href="{{ route('logout') }}"
                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal"
            >
                Выйти
            </a>
        @else
            @if (Route::currentRouteName() !== 'login')
                <a
                    href="{{ route('login') }}"
                    class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal"
                >
                    Войти
                </a>
            @endif

            @if (Route::currentRouteName() !== 'register')
                <a
                    href="{{ route('register') }}"
                    class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                    Зарегистрироваться
                </a>
            @endif
        @endauth
    </nav>
</header>
<div
    class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
    <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
        @if (auth()->user()?->is_confirmed)
            <div
                x-cloak
                x-data="notifications()"
                x-init="window.showToast = (msg) => { addToast(msg) }"
                class="fixed z-50 bottom-4 right-4 flex flex-col gap-2 items-end"
                style="pointer-events: none;"
            >
                <template x-for="toast in toasts" :key="toast.id">
                    <div
                        class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-3 rounded shadow-lg border border-gray-200 dark:border-gray-700 transition"
                        x-show="true"
                        x-transition
                        style="pointer-events: auto;"
                    >
                        <span x-text="toast.message"></span>
                    </div>
                </template>
            </div>
        @endif
        @yield('content')
    </main>
</div>
<div class="h-14.5 hidden lg:block"></div>
@if (auth()->user()?->is_confirmed)
    <script defer>
        function notifications() {
            return {
                toasts: [],
                nextId: 1,
                addToast(message) {
                    const id = this.nextId++;
                    this.toasts.push({ id, message });
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 10000);
                }
            };
        }

        let retryTimeout = 1000;

        function connectSSE() {
            const sse = new EventSource('{{ route('sse') }}');

            sse.onmessage = function (event) {
                console.log('SSE message:', event.data);
            };

            sse.addEventListener('news_added', function (event) {
                const data = JSON.parse(event.data);
                window.showToast(data.message);
            });

            sse.addEventListener('user_registered', function (event) {
                const data = JSON.parse(event.data);
                window.showToast(data.message);
            });

            sse.addEventListener('user_confirmed', function (event) {
                const data = JSON.parse(event.data);
                window.showToast(data.message);
            });

            sse.onerror = function (e) {
                console.error('SSE error', e);
                sse.close();
                setTimeout(() => {
                    retryTimeout = Math.min(retryTimeout * 2, 30000);
                    connectSSE();
                }, retryTimeout);
            };

            sse.onopen = function () {
                console.log('SSE connection established');
                retryTimeout = 1000;
            };
        }

        document.addEventListener('DOMContentLoaded', () => {
            connectSSE();
        });
    </script>
@endif
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
