@extends('layouts.app')

@section('title', "Edit NPC Spell: {$npcSpell->name}")
@section('page-title', "Edit NPC Spell: {$npcSpell->name}")

@section('content')
    <div class="grid grid-cols-1 gap-6">
        <form method="POST" action="{{ route('npc-spells.update', $npcSpell) }}">
            @csrf
            @method('PUT')
            @include('npc-spells.forms.form', ['npcSpell' => $npcSpell])
        </form>

        <div class="divider"></div>

        @include('npc-spells.partials.index-entry', [
            'npcSpell' => $npcSpell,
            'modalScope' => 'main-set'
        ])

        @if ($npcSpell->parentSet)
            @include('npc-spells.partials.index-entry', [
                'npcSpell' => $npcSpell->parentSet,
                'modalScope' => 'parent-set'
            ])
        @endif
    </div>
@endsection
