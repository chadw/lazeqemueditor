<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Spatie\DiscordAlerts\Facades\DiscordAlert;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $oldName = $user->name;
        $oldEmail = $user->email;

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // If the user's name or email changed, write an activity log and post a Discord alert
        $changes = [];
        if ($user->wasChanged('name')) {
            $changes['name'] = ['old' => $oldName, 'new' => $user->name];
        }
        if ($user->wasChanged('email')) {
            $changes['email'] = ['old' => $oldEmail, 'new' => $user->email];
        }

        if (!empty($changes)) {
            // Activity log (spatie/activitylog)
            try {
                activity()
                    ->causedBy($user)
                    ->performedOn($user)
                    ->withProperties([
                        'old' => array_map(fn($v) => $v['old'], $changes),
                        'attributes' => array_map(fn($v) => $v['new'], $changes),
                    ])
                    ->log('User profile changed');
            } catch (\Throwable $e) {
                // don't break the request on logging failures
            }

            // Discord alert (uses Spatie Discord Alerts facade)
            try {
                $parts = [];
                foreach ($changes as $field => $vals) {
                    $parts[] = sprintf('%s **%s** ➜ **%s**', ucfirst($field), $vals['old'] ?? 'NULL', $vals['new'] ?? 'NULL');
                }
                $webhookMessage = sprintf('User profile changed: %s (id: %d)', implode(', ', $parts), $user->id);
                DiscordAlert::message($webhookMessage);
            } catch (\Throwable $e) {
                // ignore failures to avoid disrupting user flow
            }
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
