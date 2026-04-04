<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class ObjectSprite
{
    public static function ids(): array
    {
        return Cache::rememberForever('object_sprite_ids', function () {

            $path = public_path('css/objects.css');

            if (!File::exists($path)) {
                return [];
            }

            $css = File::get($path);

            preg_match_all('/\.object-(\d+)\s*\{/', $css, $matches);

            return collect($matches[1])
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->sort()
                ->values()
                ->toArray();
        });
    }
}
