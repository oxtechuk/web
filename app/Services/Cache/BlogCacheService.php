<?php

namespace App\Services\Cache;

use App\Models\BlogPost;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class BlogCacheService extends BaseCacheService
{
    public function rememberBlogIndex(int $page): array
    {
        $version = $this->getBlogVersion();

        return $this->remember("blog.index.v{$version}.page.{$page}", function () use ($page) {
            $featuredPosts = BlogPost::published()
                ->where('is_featured', true)
                ->latest('published_at')
                ->take(3)
                ->get();

            $featuredIds = $featuredPosts->pluck('id');

            $posts = BlogPost::published()
                ->whereNotIn('id', $featuredIds)
                ->latest('published_at')
                ->paginate(9);

            return compact('featuredPosts', 'posts');
        });
    }

    public function rememberBlogHero(): array
    {
        return $this->remember('blog.hero', function () {
            $heroSetting = Setting::where('key', 'store_blog_hero')->first();

            return $heroSetting ? $heroSetting->value : [
                'title' => 'مدونة <span class="highlight">جي آر موتورز</span>',
                'subtitle' => 'تابع أحدث أخبار السيارات، النصائح، والمقالات الحصرية.',
                'image' => null,
            ];
        }, self::TTL_LONG);
    }

    public function forgetBlog(): void
    {
        Cache::forget('blog.hero');
        Cache::increment('blog.cache_version');
    }

    private function getBlogVersion(): int
    {
        return Cache::remember('blog.cache_version', self::TTL_LONG, fn () => 1);
    }
}
