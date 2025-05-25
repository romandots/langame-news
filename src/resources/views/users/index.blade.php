@extends('layouts.app')

@section('content')
    <div
        x-data="usersPage()"
        x-init="fetchUsers()"
        class="container mx-auto py-8"
    >
        <template x-if="loading">
            <div class="text-center py-8 text-gray-700 dark:text-gray-300">Загрузка...</div>
        </template>

        <template x-if="!loading && users.length === 0">
            <div class="text-center py-8 text-gray-700 dark:text-gray-300">Пользователи не найдены.</div>
        </template>

        <div x-if="!loading && users.length > 0" class="overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                <thead>
                <tr>
                    <th class="px-4 py-2 text-gray-900 dark:text-gray-100">ID</th>
                    <th class="px-4 py-2 text-gray-900 dark:text-gray-100">Имя</th>
                    <th class="px-4 py-2 text-gray-900 dark:text-gray-100">Email</th>
                    <th class="px-4 py-2 text-gray-900 dark:text-gray-100">Подтверждён</th>
                    <th class="px-4 py-2 text-gray-900 dark:text-gray-100">Код подтверждения</th>
                    <th class="px-4 py-2 text-gray-900 dark:text-gray-100">Создан</th>
                </tr>
                </thead>
                <tbody>
                <template x-for="user in users" :key="user.id">
                    <tr :class="!user.is_confirmed ? 'bg-gray-200 dark:bg-gray-800 text-gray-500' : ''">
                        <td class="px-4 py-2" x-text="user.id"></td>
                        <td class="px-4 py-2" x-text="user.name"></td>
                        <td class="px-4 py-2" x-text="user.email"></td>
                        <td class="px-4 py-2" x-text="user.is_confirmed ? 'Да' : 'Нет'"></td>
                        <td class="px-4 py-2" x-text="user.confirmation_code"></td>
                        <td class="px-4 py-2" x-text="user.created_at"></td>
                    </tr>
                </template>
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-center space-x-2" x-show="pages > 1">
            <template x-for="n in pages" :key="n">
                <button
                    class="px-3 py-1 border rounded bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100"
                    :class="{'bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-gray-100': page === n}"
                    @click="fetchUsers(n)"
                    x-text="n"
                    :disabled="page === n"
                ></button>
            </template>
        </div>
    </div>

    <script>
        function usersPage() {
            return {
                users: [],
                page: 1,
                pages: 1,
                loading: false,
                fetchUsers(page = 1) {
                    this.loading = true;
                    this.page = page;
                    fetch(`{{ route('users.fetch') }}?page=${page}`)
                        .then(r => r.json())
                        .then(data => {
                            this.users = data.data;
                            this.pages = data.last_page;
                            this.page = data.current_page;
                        })
                        .finally(() => this.loading = false);
                }
            }
        }
    </script>
@endsection
