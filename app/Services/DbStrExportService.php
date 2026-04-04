<?php

namespace App\Services;

use App\Models\DbStr;

class DbStrExportService
{
    public function export(): array
    {
        $filename = 'dbstr_us.txt';
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

        DbStr::orderBy('id')
            ->orderBy('type')
            ->chunk(1000, function ($rows) use (&$count, &$lastId, $handle) {
                foreach ($rows as $row) {
                    $data = $row->getAttributes();

                    // append ^0 at the end of the line
                    fwrite($handle, implode('^', $data) . '^0' . PHP_EOL);

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
