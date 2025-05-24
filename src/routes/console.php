<?php

use Illuminate\Support\Facades\Schedule;

$newsSources = config('news.sources');
foreach ($newsSources as $source => $config) {
    if ($config['enabled'] !== true) {
        continue;
    }
    Schedule::command("news:aggregate {$source}")
        ->everyFiveMinutes()
        ->withoutOverlapping()
        ->runInBackground();
};
