<?php

namespace App\Repositories;

use App\DTO\News;
use App\Models\News as NewsModel;
use Illuminate\Database\Eloquent\Collection;

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

    /**
     * @param string|null $search
     * @param int $page
     * @param int $itemsPerPage
     * @return array{items: Collection, total: int}
     */
    public function search(?string $search, int $page, int $itemsPerPage): array
    {
        $query = NewsModel::query()->when($search, function ($query, $search) {
            return $query->whereRaw(
                "MATCH(title, summary, description) AGAINST (? IN BOOLEAN MODE)",
                [$search]
            );
        });

        $total = $query->count();
        $lastPage = (int)ceil($total / $itemsPerPage);
        $currentPage = min($page, $lastPage);
        $items = $query->orderBy('published_at', 'desc')
            ->skip(($currentPage - 1) * $itemsPerPage)
            ->take($itemsPerPage)
            ->get();

        return [
            'items' => $items,
            'total' => $total,
        ];
    }
}
