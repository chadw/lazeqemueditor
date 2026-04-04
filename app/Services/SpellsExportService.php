<?php

namespace App\Services;

use App\Models\Spell;

class SpellsExportService
{
    public function export(): array
    {
        $filename = 'spells_us.txt';
        $count = 0;
        $lastId = 0;

        $path = storage_path("app/exports/{$filename}");

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $handle = fopen($path, 'w');
        if (! $handle) {
            throw new \RuntimeException("Unable to open {$filename} for writing.");
        }

        Spell::orderBy('id')
            ->chunk(1000, function ($rows) use (&$count, &$lastId, $handle) {
                foreach ($rows as $row) {
                    $data = $row->getAttributes();

                    fwrite($handle, implode('^', $data) . PHP_EOL);

                    $lastId = $row->id;
                    $count++;
                }
            });

        fclose($handle);

        return [
            'success' => $count > 0,
            'count'   => $count,
            'lastid'  => $lastId,
            'file'    => $filename,
            'path'    => $path,
        ];
    }
}
