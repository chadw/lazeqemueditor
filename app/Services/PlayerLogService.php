<?php

namespace App\Services;

use App\Models\Item;
use App\Models\CharacterData;
use App\Models\Guild;
use Illuminate\Pagination\LengthAwarePaginator;

class PlayerLogService
{
    /**
     * Hydrate a paginated collection of PlayerEventLogs
     * with resolved items, characters, and guilds.
     *
     * @param  mixed $logs
     * @return LengthAwarePaginator
     */
    public function hydratePaginated($logs): LengthAwarePaginator
    {
        [$itemIds, $charIds, $guildIds] = $this->extractIds($logs);

        $items = Item::whereIn('id', $itemIds)->get(['id', 'Name', 'icon'])->keyBy('id');
        $characters = CharacterData::whereIn('id', $charIds)->get(['id', 'name'])->keyBy('id');
        $guilds = Guild::whereIn('id', $guildIds)->get(['id', 'name'])->keyBy('id');

        $logs->getCollection()->transform(function ($log) use ($items, $characters, $guilds) {
            $log->event_data = $this->hydrateEventData($log->event_data ?? [], $items, $characters, $guilds);
            return $log;
        });

        return $logs;
    }

    /**
     * Extract item_id, char_id, guild_id from event_data
     *
     * @param  mixed $logs
     * @return array
     */
    protected function extractIds($logs): array
    {
        $itemIds = collect();
        $charIds = collect();
        $guildIds = collect();

        foreach ($logs as $log) {
            $data = $log->event_data ?? [];
            array_walk_recursive($data, function ($value, $key) use ($itemIds, $charIds, $guildIds) {
                if (is_numeric($value)) {
                    if ($key === 'item_id') $itemIds->push((int)$value);
                    if ($key === 'char_id') $charIds->push((int)$value);
                    if ($key === 'guild_id') $guildIds->push((int)$value);
                }
            });
        }

        return [
            $itemIds->unique()->filter()->values(),
            $charIds->unique()->filter()->values(),
            $guildIds->unique()->filter()->values(),
        ];
    }

    /**
     * hydrateEventData
     *
     * @param  mixed $data
     * @param  mixed $items
     * @param  mixed $characters
     * @param  mixed $guilds
     * @return array
     */
    protected function hydrateEventData(array $data, $items, $characters, $guilds): array
    {
        array_walk_recursive($data, function (&$value, $key) use ($items, $characters, $guilds) {
            if ($key === 'item_id' && isset($items[$value])) {
                $value = [
                    'id'   => $value,
                    'item' => $items[$value],
                ];
            }

            if ($key === 'char_id' && isset($characters[$value])) {
                $value = [
                    'id'        => $value,
                    'character' => $characters[$value],
                ];
            }

            if ($key === 'guild_id' && isset($guilds[$value])) {
                $value = [
                    'id'    => $value,
                    'guild' => $guilds[$value],
                ];
            }
        });

        return $data;
    }
}
