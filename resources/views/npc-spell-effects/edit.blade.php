@extends('layouts.app')
@section('title', "Edit NPC Spell Effect: {$npcSpellEffect->name}")
@section('page-title', "Edit NPC Spell Effect: {$npcSpellEffect->name}")

@section('content')
    <div class="grid grid-cols-1 gap-6">
        <form method="POST" action="{{ route('npc-spell-effects.update', $npcSpellEffect) }}">
            @csrf
            @method('PUT')
            @include('npc-spell-effects.forms.form', ['npcSpellEffect' => $npcSpellEffect])
        </form>

        <div class="divider"></div>

        @include('npc-spell-effects.partials.index-entry', ['npcSpellEffect' => $npcSpellEffect])
    </div>
@endsection
