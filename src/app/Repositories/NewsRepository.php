<?php

namespace App\Repositories;

use App\DTO\News;
use App\Models\News as NewsModel;

class NewsRepository
{
    public function create(News $news): NewsModel
    {
        return NewsModel::create([
            'title' => $news->title,
            'summary' => $news->summary,
            'description' => $news->description,
            'url' => $news->url,
            'source' => $news->source,
            'ext_id' => $news->ext_id,
            'published_at' => $news->published_at,
        ]);
    }

    public function getLastPublishedDateForSource(string $source): ?\DateTime
    {
        return NewsModel::where('source', $source)
            ->orderBy('published_at', 'desc')
            ->first()
            ?->published_at;
    }
}
