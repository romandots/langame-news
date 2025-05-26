<?php

return [
    'sources' => [
        'newsdata' => [
            'aggregator' => \App\Services\News\NewsAggregators\NewsdataNewsAggregator::class,
            'url' => env('NEWSDATA_URL'),
            'enabled' => env('NEWSDATA_ENABLED', false),
        ],
        'un_news' => [
            'aggregator' => \App\Services\News\NewsAggregators\UnNewsRssAggregator::class,
            'url' => env('UN_NEWS_URL'),
            'enabled' => env('UN_NEWS_ENABLED', false),
        ],
    ],
    'search' => [
        'items_per_page' => 10,
    ],
];
