@extends('layouts.app')
@section('title', 'Trader Audit Logs')
@section('page-title', 'Trader Audit Logs')

@section('content')
    <div>
        <x-top-links>
            <x-slot name="left">
                @include('trader-audit.partials.filters')
            </x-slot>
        </x-top-links>

        <x-search-results :items="$logs" title="Trader Audit Logs">
            <x-ui.table height="overflow-x-auto max-h-[70vh] overflow-y-auto" theadsticky="top-0 z-10">
                <x-slot:head>
                    <tr>
                        <th class="w-[10%]">@sortablelink('seller', 'Seller')</th>
                        <th class="w-[10%]">@sortablelink('buyer', 'Buyer')</th>
                        <th>@sortablelink('itemname', 'Item')</th>
                        <th class="w-[5%] text-center">@sortablelink('quantity', 'Qty')</th>
                        <th class="w-[10%] text-right">Total</th>
                        <th class="w-[10%]">@sortablelink('time', 'Time')</th>
                        <th class="w-[5%] text-right">-</th>
                    </tr>
                </x-slot:head>
                <x-slot:body>
                    @forelse ($logs as $log)
                        <tr>
                            <td>
                                <div class="flex flex-col">
                                    <span>
                                        @if($log->sellerCharacter)
                                            <a href="{{ route('characters.show', $log->sellerCharacter) }}"
                                                class="text-base link-info link-hover">
                                                {{ $log->seller }}
                                            </a>
                                        @else
                                            {{ $log->seller }}
                                        @endif
                                    </span>
                                    @if($log->seller_model)
                                        <span class="text-xs text-base-content/60">
                                            Lvl {{ $log->seller_model->level }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-col">
                                    <span>
                                        @if($log->buyerCharacter)
                                            <a href="{{ route('characters.show', $log->buyerCharacter) }}"
                                                class="text-base link-info link-hover">
                                                {{ $log->buyer }}
                                            </a>
                                        @else
                                            {{ $log->buyer }}
                                        @endif
                                    </span>
                                    @if($log->buyer_model)
                                        <span class="text-xs text-base-content/60">
                                            Lvl {{ $log->buyer_model->level }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($log->item)
                                    <x-item-link
                                        :item_id="$log->item->id"
                                        :item_name="$log->item->Name"
                                        :item_icon="$log->item->icon"
                                        item_class="flex"
                                    />
                                @endif
                            </td>
                            <td class="text-center">
                                {{ number_format($log->quantity) }}
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1 text-sm">
                                    <x-currency :value="$log->totalcost" />
                                </div>
                            </td>
                            <td>
                                {{ $log->time?->format('Y-m-d H:i') }}
                            </td>
                            <td class="text-right">
                                <form action="{{ route('trader-audit.destroy') }}"
                                        method="POST"
                                        class="inline">
                                    @csrf
                                    @method('DELETE')

                                    <input type="hidden" name="time" value="{{ $log->time }}">
                                    <input type="hidden" name="seller" value="{{ $log->seller }}">
                                    <input type="hidden" name="buyer" value="{{ $log->buyer }}">
                                    <input type="hidden" name="itemname" value="{{ $log->itemname }}">

                                    <button
                                        class="btn btn-sm btn-soft btn-error"
                                        onclick="return confirm('Delete this audit entry?')"
                                    >
                                        <x-ui.icon name="delete" />
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-base-content/50">
                                No trader audit entries found.
                            </td>
                        </tr>
                    @endforelse
                </x-slot:body>
            </x-ui.table>
        </x-search-results>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
@endsection
