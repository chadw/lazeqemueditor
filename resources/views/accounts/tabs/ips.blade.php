<x-ui.table height="overflow-x-auto max-h-[73vh] overflow-y-auto" theadsticky="top-0 z-10">
    <x-slot:head>
        <tr>
            <th scope="col">IP</th>
            <th scope="col" class="w-[5%]">Count</th>
            <th scope="col" class="text-right w-[20%]">Last Login</th>
        </tr>
    </x-slot:head>
    <x-slot:body>
        @forelse ($account->ips as $ip)
            <tbody x-data="{ open: false, loading: false, loaded: false }" data-url="{{ route('accounts.ips.others', ['account' => $account->id, 'ip' => rawurlencode($ip->ip)]) }}">
                <tr class="{{ $loop->odd ? 'bg-base-200' : '' }}" >
                    <td scope="row" class="cursor-pointer" @click.prevent="if(!loaded){ loading=true; fetch($el.closest('tbody').dataset.url).then(r=>r.text()).then(html=>{ $refs.container.innerHTML = html; loaded=true; loading=false; open = true }).catch(()=>{ loading=false }) } else { open = !open }">
                        <div class="flex items-center gap-2">
                            <span class="font-medium">{{ $ip->ip }}</span>
                            <span class="text-xs text-base-content/50">
                                <span x-show="!open" x-cloak class="inline-flex items-center">
                                    <x-ui.icon name="chevron-down" class="h-4 w-4" />
                                </span>
                                <span x-show="open" x-cloak class="inline-flex items-center">
                                    <x-ui.icon name="chevron-up" class="h-4 w-4" />
                                </span>
                            </span>
                            <span x-show="loading" class="ml-2 loading loading-spinner loading-xs"></span>
                        </div>
                    </td>
                    <td>{{ $ip->count }}</td>
                    <td class="text-right">{{ $ip->lastused ? \Carbon\Carbon::parse($ip->lastused)->format('Y-m-d H:i') : '' }}</td>
                </tr>

                <tr x-show="open" x-cloak class="is-expanded-row">
                    <td colspan="3" class="p-2">
                        <div x-ref="container">
                            <div class="text-sm text-base-content/60">Expand to load other accounts.</div>
                        </div>
                    </td>
                </tr>
            </tbody>
        @empty
            <tr>
                <td colspan="3" class="text-center py-6 text-base-content/50">
                    No ip data.
                </td>
            </tr>
        @endforelse
    </x-slot:body>
</x-ui.table>
