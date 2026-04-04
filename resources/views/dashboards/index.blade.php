@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    <div class="mb-6">
        <x-ui.alert-info>
            <p class="text-xl">This dashboard will eventually be something. For now it's nothing but 💩</p>
        </x-ui.alert-info>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="col-span-2">
            <div class="card p-4 mb-6">
                <h2 class="text-lg font-semibold mb-2">Changelog</h2>
                <p class="text-sm text-base-content/70">Recent changes will appear here.</p>

                <div class="mt-4">
                    <div class="text-sm text-base-content/70">Installed version</div>
                    <div class="font-medium">{{ $currentVersion ?? 'unknown' }}</div>
                </div>

                @if(!empty($latest) && !empty($latest['tag_name']))
                    <div class="mt-3">
                        <div class="text-sm text-base-content/70">Latest release</div>
                        <div class="font-medium">{{ $latest['tag_name'] }} — {{ $latest['name'] ?? '' }}</div>
                        <a href="{{ $latest['html_url'] }}" target="_blank" class="link text-sm">View release on GitHub</a>
                    </div>
                @else
                    <div class="mt-3 text-sm text-base-content/60">No remote release information configured.</div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('dashboard.changelog') }}" class="btn btn-sm">Fetch changelog JSON</a>
                </div>
            </div>

            <div class="card p-4">
                <h2 class="text-lg font-semibold mb-2">Recent activity</h2>
                @if($recentActivities->isEmpty())
                    <p class="text-sm text-base-content/70">No recent activity found.</p>
                @else
                    <ul class="list-disc pl-5">
                        @foreach($recentActivities as $act)
                            <li class="text-sm">{{ $act->description }} <span class="text-xs text-base-content/60">({{ $act->created_at }})</span></li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <aside>
            <div class="card p-4 mb-6">
                <h3 class="font-semibold">Quick Links</h3>
                <ul class="mt-2 space-y-2">
                    <li><a href="{{ route('zones.index') }}" class="link">Zones</a></li>
                    <li><a href="{{ route('items.index') }}" class="link">Items</a></li>
                    <li><a href="{{ route('npcs.index') }}" class="link">NPCs</a></li>
                    <li><a href="{{ route('player-logs.index') }}" class="link">Player Logs</a></li>
                </ul>
            </div>

            <div class="card p-4">
                <h3 class="font-semibold">Stats</h3>
                <div class="mt-3">
                    <div class="flex justify-between">
                        <div class="text-sm text-base-content/70">Registered users</div>
                        <div class="font-medium">{{ number_format($userCount) }}</div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
@endsection
