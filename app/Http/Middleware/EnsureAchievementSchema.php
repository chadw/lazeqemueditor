<?php

namespace App\Http\Middleware;

use App\Services\AchievementSchemaGuard;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAchievementSchema
{
    public function __construct(private readonly AchievementSchemaGuard $schema) {}

    public function handle(Request $request, Closure $next, string $scope = 'content'): Response
    {
        $includeCharacterState = $scope === 'state';
        $issues = $this->schema->issues($includeCharacterState);
        if ($issues === []) {
            return $next($request);
        }

        $message = $includeCharacterState
            ? 'Install EQEmu content update 9329 and character update 9330, then reload this page.'
            : 'Install EQEmu content update 9329, then reload this page.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'The achievement editor requires the final EQEmu achievement schema.',
                'action' => $message,
                'issues' => $issues,
            ], 503);
        }

        return response()->view('errors.achievement-schema', [
            'includeCharacterState' => $includeCharacterState,
            'action' => $message,
            'issues' => $issues,
        ], 503);
    }
}
