@extends('layouts.app')

@section('content')
    <div
        x-data="newsPage()"
        x-init="fetchNews()"
        class="container mx-auto py-8"
    >
        <div class="mb-4">
            <input
                type="text"
                x-model="search"
                @input.debounce.500ms="fetchNews(1)"
                placeholder="Поиск новостей..."
                class="border rounded px-3 py-2 w-full bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100"
            >
        </div>

        <template x-if="loading">
            <div class="text-center py-8 text-gray-700 dark:text-gray-300">Загрузка...</div>
        </template>

        <template x-if="!loading && news.length === 0">
            <div class="text-center py-8 text-gray-700 dark:text-gray-300">Новости не найдены.</div>
        </template>

        <div x-if="!loading && news.length > 0" class="space-y-4">
            <template x-for="item in news" :key="item.id">
                <div x-html="renderEntry(item)"></div>
            </template>
        </div>

        <div class="mt-6 flex justify-center space-x-2" x-show="pages > 1">
            <template x-for="n in pages" :key="n">
                <button
                    class="px-3 py-1 border rounded bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100"
                    :class="{'bg-gray-200 dark:bg-gray-800': page === n}"
                    @click="fetchNews(n)"
                    x-text="n"
                    :disabled="page === n"
                ></button>
            </template>
        </div>
    </div>

    <script>
        function newsPage() {
            return {
                news: [],
                page: 1,
                pages: 1,
                search: '',
                loading: false,
                fetchNews(page = 1) {
                    this.loading = true;
                    this.page = page;
                    fetch(`{{ route('news.fetch') }}?page=${page}&search=${encodeURIComponent(this.search)}`)
                        .then(r => r.json())
                        .then(data => {
                            this.news = data.data;
                            this.pages = data.last_page;
                            this.page = data.current_page;
                        })
                        .finally(() => this.loading = false);
                },
                renderEntry(item) {
                    return item.html;
                }
            }
        }
    </script>
@endsection
