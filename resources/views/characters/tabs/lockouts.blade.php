<div>
	<x-ui.table>
		<x-slot:head>
			<tr>
				<th>Expedition</th>
				<th>Event</th>
				<th class="text-right w-[20%]">Expires In</th>
				<th class="text-right w-[10%]">-</th>
			</tr>
		</x-slot:head>
		<x-slot:body>
			@forelse($character->lockouts as $lockout)
				@php
					$now = now();
					$expiresAt = $lockout->expire_time;
					$remainingSeconds = $expiresAt ? $expiresAt->getTimestamp() - $now->getTimestamp() : null;
					if ($remainingSeconds === null) {
						$remaining = 'Unknown';
						$expired = false;
					} elseif ($remainingSeconds <= 0) {
						$remaining = 'Expired';
						$expired = true;
					} else {
						$d = intdiv($remainingSeconds, 86400);
						$h = intdiv($remainingSeconds % 86400, 3600);
						$m = intdiv(($remainingSeconds % 3600), 60);
						$remaining = "{$d}d {$h}h {$m}m";
						$expired = false;
					}
				@endphp
				<tr>
					<td>{{ $lockout->expedition_name }}</td>
					<td>{{ $lockout->event_name }}</td>
					<td class="text-right">
						@if($expired)
							<span class="text-error font-medium">{{ $remaining }}</span>
						@else
							<span class="font-medium">{{ $remaining }}</span>
						@endif
					</td>
					<td class="text-right">
						<form action="{{ route('dynamiczones.character-lockouts.destroy', $lockout) }}" method="POST"
                            onsubmit="return confirm('Delete this lockout for the character?');">
							@csrf
							@method('DELETE')
							<button type="submit" class="btn btn-sm btn-soft btn-error" title="Delete lockout">
                                <x-ui.icon name="delete" />
                            </button>
						</form>
					</td>
				</tr>
			@empty
				<tr>
					<td colspan="3" class="text-center italic opacity-60">
                        No dynamic zone lockouts for this character.
                    </td>
				</tr>
			@endforelse
		</x-slot:body>
	</x-ui.table>
</div>
