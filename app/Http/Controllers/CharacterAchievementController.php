<?php

namespace App\Http\Controllers;

use App\Exceptions\CharacterAchievementMutationException;
use App\Http\Requests\DiscardCharacterAchievementMutationRequest;
use App\Http\Requests\ForceCompleteCharacterAchievementRequest;
use App\Http\Requests\MarkCharacterAchievementRewardRetryableRequest;
use App\Http\Requests\ResetCharacterAchievementRequest;
use App\Http\Requests\SetCharacterAchievementProgressRequest;
use App\Models\CharacterData;
use App\Services\CharacterAchievementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CharacterAchievementController extends Controller
{
    public function __construct(private CharacterAchievementService $achievements) {}

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
        ]);
        $characters = $this->achievements
            ->paginateCharacters($filters['q'] ?? null)
            ->withQueryString();

        return view('character-achievements.index', compact('characters'));
    }

    public function show(Request $request, CharacterData $character): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
            'state' => ['nullable', Rule::in(array_keys(CharacterAchievementService::DURABLE_STATES))],
        ]);
        $payload = $this->achievements->catalog($character->id, $filters);
        $achievements = $payload['achievements'];
        $categories = $payload['categories'];
        $metadata = $payload['metadata'];

        return view(
            'character-achievements.show',
            compact('character', 'achievements', 'categories', 'metadata')
        );
    }

    public function updateProgress(
        SetCharacterAchievementProgressRequest $request,
        CharacterData $character,
        int $achievement,
        int $componentType,
        int $component
    ): RedirectResponse {
        try {
            $result = $this->achievements->setExactProgress(
                $character->id,
                $achievement,
                $componentType,
                $component,
                (int) $request->validated('current_count')
            );
        } catch (CharacterAchievementMutationException $exception) {
            return $this->mutationFailed($exception);
        }

        toast()->success(
            'Progress saved',
            "Component progress is {$result['current_count']} / {$result['required_count']}."
        );

        return back();
    }

    public function forceComplete(
        ForceCompleteCharacterAchievementRequest $request,
        CharacterData $character,
        int $achievement
    ): RedirectResponse {
        try {
            $this->achievements->forceCompleteOffline($character->id, $achievement);
        } catch (CharacterAchievementMutationException $exception) {
            return $this->mutationFailed($exception);
        }

        toast()->warning(
            'Offline completion forced',
            '%s now has durable completion state. No live earned notification was sent; rewards and dependent achievements are reconciled when the character next loads.',
            [$character->name]
        );

        return back();
    }

    public function reset(
        ResetCharacterAchievementRequest $request,
        CharacterData $character,
        int $achievement
    ): RedirectResponse {
        $resetRewards = $request->boolean('reset_rewards');

        try {
            $this->achievements->reset($character->id, $achievement, $resetRewards);
        } catch (CharacterAchievementMutationException $exception) {
            return $this->mutationFailed($exception);
        }

        if ($resetRewards) {
            toast()->warning(
                'Achievement and rewards reset',
                'Completion, progress, queued mutations, reward selections, and reward ledgers were removed. Recompletion can grant rewards again.'
            );
        } else {
            toast()->success(
                'Achievement reset',
                'Completion, progress, and queued mutations were removed. Reward ledgers were preserved.'
            );
        }

        return back();
    }

    public function markRewardRetryable(
        MarkCharacterAchievementRewardRetryableRequest $request,
        CharacterData $character,
        int $achievement,
        int $reward
    ): RedirectResponse {
        try {
            $this->achievements->markRewardRetryable(
                $character->id,
                $achievement,
                $reward
            );
        } catch (CharacterAchievementMutationException $exception) {
            return $this->mutationFailed($exception);
        }

        toast()->warning(
            'Reward marked retryable',
            'The server may attempt this individual grant again. Duplicate-delivery risk was explicitly accepted.'
        );

        return back();
    }

    public function markSelectionRetryable(
        MarkCharacterAchievementRewardRetryableRequest $request,
        CharacterData $character,
        int $achievement,
        int $rewardSet
    ): RedirectResponse {
        try {
            $this->achievements->markSelectionRetryable(
                $character->id,
                $achievement,
                $rewardSet
            );
        } catch (CharacterAchievementMutationException $exception) {
            return $this->mutationFailed($exception);
        }

        toast()->warning(
            'Reward selection marked retryable',
            'The server may resume this selected bundle. Duplicate-delivery risk was explicitly accepted; inspect its individual reward ledgers too.'
        );

        return back();
    }

    public function retryMutation(
        CharacterData $character,
        int $achievement,
        int $mutation
    ): RedirectResponse {
        try {
            $this->achievements->retryBlockedMutation(
                $character->id,
                $achievement,
                $mutation
            );
        } catch (CharacterAchievementMutationException $exception) {
            return $this->mutationFailed($exception);
        }

        toast()->success(
            'Mutation queued for retry',
            'The blocked status and lease diagnostic were cleared. The definition-version guard remains in force.'
        );

        return back();
    }

    public function discardMutation(
        DiscardCharacterAchievementMutationRequest $request,
        CharacterData $character,
        int $achievement,
        int $mutation
    ): RedirectResponse {
        try {
            $this->achievements->discardMutation(
                $character->id,
                $achievement,
                $mutation
            );
        } catch (CharacterAchievementMutationException $exception) {
            return $this->mutationFailed($exception);
        }

        toast()->warning(
            'Queued mutation discarded',
            'The authored request was deleted without applying it to character progress.'
        );

        return back();
    }

    private function mutationFailed(
        CharacterAchievementMutationException $exception
    ): RedirectResponse {
        toast()->error('Achievement update failed', $exception->getMessage());

        return back();
    }
}
