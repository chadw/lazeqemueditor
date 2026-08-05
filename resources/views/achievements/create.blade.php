@extends('layouts.app')
@section('title', 'Create Achievement')
@section('page-title', 'Create Achievement')

@section('content')
    @include('achievements.form', [
        'formAction' => route('achievements.store'),
        'formMethod' => 'POST',
        'isCreate' => true,
    ])
@endsection
