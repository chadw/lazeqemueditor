@extends('layouts.app')
@section('title', 'Client Files')
@section('page-title', 'Client Files')

@section('content')
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="card bg-base-200 shadow-sm hover:shadow-md transition">
                <div class="card-body text-center">
                    <h3 class="card-title">DBStr</h3>
                    <p class="text-sm opacity-70">Export dbstr_us.txt</p>
                    <a href="{{ route('client-files.export', ['file' => 'dbstr']) }}"
                        class="btn btn-soft btn-accent mt-2">Export</a>
                </div>
            </div>
            <div class="card bg-base-200 shadow-sm hover:shadow-md transition">
                <div class="card-body text-center">
                    <h3 class="card-title">Spells</h3>
                    <p class="text-sm opacity-70">Export spells_us.txt</p>
                    <a href="{{ route('client-files.export', ['file' => 'spells']) }}"
                        class="btn btn-soft btn-accent mt-2">Export</a>
                </div>
            </div>
            <div class="card bg-base-200 shadow-sm hover:shadow-md transition">
                <div class="card-body text-center">
                    <h3 class="card-title">Skills</h3>
                    <p class="text-sm opacity-70">Export skills_us.txt</p>
                    <a href="{{ route('client-files.export', ['file' => 'skills']) }}"
                        class="btn btn-soft btn-accent mt-2">Export</a>
                </div>
            </div>
            <div class="card bg-base-200 shadow-sm hover:shadow-md transition">
                <div class="card-body text-center">
                    <h3 class="card-title">BaseData</h3>
                    <p class="text-sm opacity-70">Export basedata_us.txt</p>
                    <a href="{{ route('client-files.export', ['file' => 'basedata']) }}"
                        class="btn btn-soft btn-accent mt-2">Export</a>
                </div>
            </div>
        </div>
    </div>
@endsection
