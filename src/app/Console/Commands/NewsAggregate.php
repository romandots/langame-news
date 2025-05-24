<?php

namespace App\Console\Commands;

use App\Services\News\Contracts\NewsAggregatorInterface;
use App\Services\News\NewsService;
use Illuminate\Console\Command;

class NewsAggregate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:aggregate {source : Источник новостей}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Аггрегация новостей из источника';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $source = $this->argument('source');
        $aggregatorClass = config("news.sources.{$source}.aggregator");

        if (empty($aggregatorClass) || !class_exists($aggregatorClass)) {
            $this->error("Не найден аггрегатор для источника: {$source}");
            return;
        }

        if (!is_subclass_of($aggregatorClass, \App\Services\News\Contracts\NewsAggregatorInterface::class)) {
            $this->error("Аггрегатор для источника: {$source} не реализует интерфейс NewsAggregatorInterface");
            return;
        }

        /** @var NewsAggregatorInterface $service */
        $aggregator = app()->make($aggregatorClass);

        /** @var NewsService $service */
        $service = app()->make(NewsService::class);

        $this->info("Запускаем аггрегацию новостей из источника: {$source}");
        $count = $service->aggregateNews($aggregator);
        $this->info("Аггрегация новостей из источника: {$source} завершена. Добавлено новостей: {$count}");
    }
}
