<div class="border rounded p-4 mb-4 bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700" id="news-{{ $news['id'] ?? $news->id }}">
    <div class="flex justify-between items-center mb-2">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
            <a href="{{ $news['url'] ?? $news->url }}" target="_blank" class="hover:underline">
                {{ $news['title'] ?? $news->title }}
            </a>
        </h2>
        <span class="text-xs text-gray-500 dark:text-gray-400">
            {{ $news['published_at'] ?? $news->published_at }}
        </span>
    </div>
    <div class="mb-2 text-gray-700 dark:text-gray-300">
        {{ $news['summary'] ?? $news->summary }}
    </div>
    <div class="mb-2 text-gray-800 dark:text-gray-200">
        {{ $news['description'] ?? $news->description }}
    </div>
    <div class="text-sm text-gray-500 dark:text-gray-400">
        Источник: {{ $news['source'] ?? $news->source }}
    </div>
</div>
