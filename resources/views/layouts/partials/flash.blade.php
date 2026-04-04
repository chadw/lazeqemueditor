@if (session('success'))
    <div class="alert alert-soft alert-success mb-4">
        {{ session('success') }}
    </div>
@endif

@if (session('info'))
    <div class="alert alert-soft alert-info mb-4">
        {{ session('info') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-soft alert-error mb-4">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-soft alert-error mb-4">
        <ul class="list-disc ml-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
