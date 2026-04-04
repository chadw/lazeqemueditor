@extends('layouts.app')
@section('title', 'Create new AA')
@section('page-title', 'Create new AA')

@section('content')

    <x-top-links>
        <x-slot name="left">
            @include('aa.partials.filters')
        </x-slot>
    </x-top-links>

    <div class="grid grid-cols-1 gap-6">
        <form method="POST" action="{{ route('aa.store') }}">
            @csrf

            @include('aa.forms.form-ability')

            <div class="flex justify-end gap-2 mt-6">
                <button type="submit" class="btn btn-sm btn-soft btn-success">
                    <x-ui.icon name="save" /> Save Ability
                </button>
            </div>
        </form>
    </div>
@endsection
