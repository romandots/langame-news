<?php

return [
    'sources' => [
        'newsdata' => [
            'aggregator' => \App\Services\News\NewsAggregators\NewsdataNewsAggregator::class,
            'url' => env('NEWSDATA_URL'),
            'enabled' => env('NEWSDATA_ENABLED', false),
        ],
    ],
];
