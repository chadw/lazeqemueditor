@php
    $nameClass = $attributes->get('name-class', '');
@endphp

@if (is_numeric($stat))
    @if ($stat != 0 || $stat2 != 0)
        <tr>
            <td class="pr-2 font-bold {{ $nameClass }}">{{ $name }}:</td>
            <td class="text-right">
                @if ($stat < 0)
                    <span class="text-red-600">{{ sign($stat) }}</span>
                @else
                    {{ $stat }}
                @endif
                @if ($stat2 < 0)
                    <span class="text-red-600">{{ sign($stat2) }}</span>
                @elseif ($stat2 > 0)
                    <span class="text-heroic-stat">
                        {{ sign($stat2) }}
                    </span>
                @endif
            </td>
        </tr>
    @endif
@elseif (preg_replace("/[^0-9]/", "", $stat) > 0)
    <tr>
        <td class="pr-2 font-bold {{ $nameClass }}">{{ $name }}:</td>
        <td class="text-right">{{ $stat }}</td>
    </tr>
@else
    <tr>
        <td class="pr-2 font-bold {{ $nameClass }}">{{ $name }}:</td>
        <td class="text-right">{{ $stat }}</td>
    </tr>
@endif
