@extends('layouts.app')
@section('title', "Edit LDoN Trap Template")
@section('page-title', 'Edit LDoN Trap Template')

@section('content')
    <div class="grid grid-cols-1 gap-6">
        <form method="POST" action="{{ route('ldon-trap-templates.update', $trapTemplate) }}">
            @csrf
            @method('PUT')
            @include('ldon-trap-templates.forms.form', ['trapTemplate' => $trapTemplate])
        </form>

        <div class="divider"></div>

        @include('ldon-trap-templates.partials.index-entry', ['trapTemplate' => $trapTemplate])
    </div>
@endsection
