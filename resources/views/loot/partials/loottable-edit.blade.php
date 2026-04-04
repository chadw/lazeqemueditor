<div class="card bg-base-200 mb-6">
    <div class="card-body">
        <form method="POST" action="{{ route('loot.update', $lt) }}">
            @csrf
            @method('PUT')
            @include('loot.forms.form', ['table' => $lt])
        </form>
    </div>
</div>
