@props(['others'])

@if ($others->isEmpty())
    <div class="text-sm text-base-content/60 p-2">No other accounts have used this IP.</div>
@else
    <div class="card bg-base-100 border border-info/50 shadow">
        <div class="border border-base-content/5">
            <table class="table table-auto table-zebra md:table-fixed w-full">
                <thead class="text-xs uppercase bg-neutral sticky">
                    <tr class="text-left text-xs text-base-content/60">
                        <th>Account</th>
                        <th class="w-[5%]">Count</th>
                        <th class="text-right w-[20%]">Last Login</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($others as $acctIp)
                        <tr class="border-t">
                            <td>
                                @if ($acctIp->account)
                                    <a href="{{ route('accounts.show', $acctIp->account->id) }}"
                                        class="link link-accent link-hover">{{ $acctIp->account->name }}</a>
                                @else
                                    Account {{ $acctIp->accid }}
                                @endif
                            </td>
                            <td>{{ $acctIp->count }}</td>
                            <td class="text-right">
                                {{ $acctIp->lastused ? \Carbon\Carbon::parse($acctIp->lastused)->format('Y-m-d H:i') : '' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
