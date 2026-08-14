<?php

declare(strict_types=1);

namespace App\Services\Api\Store;

use App\Models\BlogPost;
use App\Services\Cache\BlogCacheService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class BlogApiService
{
    public function __construct(
        private readonly BlogCacheService $cache,
    ) {}

    public function list(int $page = 1, int $perPage = 10): array
    {
        $data = $this->cache->rememberBlogIndex($page);

        $featuredPosts = $data['featuredPosts'] ?? collect();
        $posts = $data['posts'] ?? collect();

        return [
            'featured_posts' => $featuredPosts->toArray(),
            'posts' => $posts->toArray(),
            'meta' => $posts instanceof LengthAwarePaginator ? [
                'current_page' => $posts->currentPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'last_page' => $posts->lastPage(),
            ] : null,
        ];
    }

    public function findBySlug(string $slug): array
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $related = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return [
            'post' => $post->toArray(),
            'related_posts' => $related->toArray(),
        ];
    }
}
