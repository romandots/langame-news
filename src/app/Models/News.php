<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\News
 *
 * @property int $id
 * @property string $title
 * @property string $summary
 * @property string $description
 * @property string $url
 * @property string $source
 * @property string|null $ext_id
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\NewsFactory factory(...$parameters)
 */
class News extends Model
{
    protected $fillable = [
        'title',
        'summary',
        'description',
        'url',
        'source',
        'ext_id',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'summary' => $this->summary,
            'description' => $this->description,
            'url' => $this->url,
            'source' => $this->source,
            'published_at' => $this->published_at->toDateTimeLocalString(),
        ];
    }
}
