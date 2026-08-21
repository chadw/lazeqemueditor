@extends('layouts.app')
@section('title', 'Achievement Schema Update Required')
@section('page-title', 'Achievement Schema Update Required')

@section('content')
    <div class="alert alert-error shadow-lg">
        <div class="space-y-2">
            <h2 class="font-bold text-lg">The achievement editor was stopped before reading or changing data.</h2>
            <p>{{ $action }}</p>
            <p class="text-sm opacity-90">
                This safety check prevents the editor from mixing the retired achievement schema with the final
                shared reward catalog{{ $includeCharacterState ? ' and character update queue' : '' }}.
            </p>
        </div>
    </div>

    <div class="card bg-base-100 shadow mt-4">
        <div class="card-body">
            <h3 class="card-title">Schema checks that did not pass</h3>
            <ul class="list-disc pl-6 space-y-1 font-mono text-sm">
                @foreach ($issues as $issue)
                    <li>{{ $issue }}</li>
                @endforeach
            </ul>
            <p class="text-sm opacity-70">
                Run the normal EQEmu database update workflow for updates 9329 and 9330. Do not manually rename
                legacy columns; the official migrations also install the provider-independent reward tables.
            </p>
        </div>
    </div>
@endsection
