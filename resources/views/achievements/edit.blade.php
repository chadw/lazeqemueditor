@extends('layouts.app')
@section('title', 'Achievement: ' . $editor['name'])
@section('page-title', 'Achievement: ' . $editor['name'])

@section('content')
    @include('achievements.form', [
        'formAction' => route('achievements.update', $editor['id']),
        'formMethod' => 'PUT',
        'isCreate' => false,
    ])
@endsection
