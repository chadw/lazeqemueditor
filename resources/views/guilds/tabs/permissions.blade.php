<x-ui.table>
    <x-slot:head>
        <tr>
            <th scope="col">Permission</th>
            @foreach ($guild->ranks as $rank)
                <th scope="col">
                    <span class="guildrank-icon guildrank-{{ $rank->rank }}">
                        <span class="ps-6">{{ $rank->title }}</span>
                    </span>
                </th>
            @endforeach
        </tr>
    </x-slot:head>
    <x-slot:body>
        @forelse (config('everquest.guild_permissions') as $k => $v)
            <tr>
                @php
                    $perm = $guild->permissions->firstWhere('perm_id', $k);
                    $mask = $perm?->permission ?? 0;
                @endphp
                <td scope="row">{{ $v }}</td>
                <td>{!! $mask & 128 ? status_ok() : status_no() !!}</td>
                <td>{!! $mask & 64 ? status_ok() : status_no() !!}</td>
                <td>{!! $mask & 32 ? status_ok() : status_no() !!}</td>
                <td>{!! $mask & 16 ? status_ok() : status_no() !!}</td>
                <td>{!! $mask & 8 ? status_ok() : status_no() !!}</td>
                <td>{!! $mask & 4 ? status_ok() : status_no() !!}</td>
                <td>{!! $mask & 2 ? status_ok() : status_no() !!}</td>
                <td>{!! $mask & 1 ? status_ok() : status_no() !!}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center text-gray-500">
                    No guild permissions found.
                </td>
            </tr>
        @endforelse
    </x-slot:body>
</x-ui.table>
