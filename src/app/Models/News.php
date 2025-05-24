<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    /** @use HasFactory<\Database\Factories\NewsFactory> */
    use HasFactory;
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
}
