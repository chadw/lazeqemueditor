<?php

namespace Tests\Feature\Views;

use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class CharacterAchievementViewTest extends TestCase
{
    public function test_show_view_renders_an_empty_achievement_catalog(): void
    {
        $this->withoutVite();

        $character = (object) [
            'id' => 1015,
            'name' => 'Lyric',
            'level' => 60,
            'class' => 8,
            'ingame' => 0,
        ];
        $achievements = new LengthAwarePaginator(
            items: [],
            total: 0,
            perPage: 25,
            currentPage: 1,
            options: ['path' => '/characters/1015/achievements'],
        );
        $categories = collect();
        $metadata = [
            'force_completion_warning' => 'Offline completion does not send a live notification.',
            'filters' => [
                'q' => '',
                'category' => null,
                'state' => 'all',
            ],
            'durable_states' => [
                'all' => 'All durable states',
            ],
        ];

        $html = view(
            'character-achievements.show',
            compact('character', 'achievements', 'categories', 'metadata')
        )->render();

        $this->assertStringContainsString(
            'No achievements match these filters.',
            $html
        );
    }
}
