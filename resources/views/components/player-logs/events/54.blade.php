@php
    $d = $log->event_data;

    $channels = [
        0 => [
            'label' => 'tells the guild,',
            'class' => 'text-green-600',
        ],
        2 => [
            'label' => 'tells the group,',
            'class' => 'text-cyan-600',
        ],
        3 => [
            'label' => 'shouts,',
            'class' => 'text-red-400',
        ],
        4 => [
            'label' => 'auctions,',
            'class' => 'text-lime-600',
        ],
        5 => [
            'label' => 'says out of character,',
            'class' => 'text-lime-900',
        ],
        7 => [
            'label' => 'tells',
            'class' => 'text-purple-500',
        ],
        15 => [
            'label' => 'tells the raid,',
            'class' => 'text-cyan-400',
        ],
    ];

    $channel = $channels[$d['type']] ?? [
        'label' => 'says,',
        'class' => 'text-gray-300',
    ];
@endphp
<div class="flex flex-col gap-1">
    <div class="text-sm {{ $channel['class'] }}">
        <span class="font-medium">
            {{ $d['from'] }}
        </span>
        {{ $channel['label'] }}
        @if ($d['type'] === 7)
            <span class="font-medium">
                {{ $d['to'] }}
            </span>
        @endif
        <span class="italic">
            "{{ $d['message'] }}"
        </span>
        @if ($d['type'] === 20)
            to Unknown channel
        @elseif ($d['type'] === 8)
            to
            <span class="font-medium">
                {{ $d['to'] }}
            </span>
        @endif
    </div>
</div>
