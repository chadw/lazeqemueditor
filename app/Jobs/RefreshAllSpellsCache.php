<?php

namespace App\Jobs;

use App\Models\Spell;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class RefreshAllSpellsCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $lock = Cache::lock('refresh_all_spells_lock', 60);
        if (! $lock->get()) {
            return;
        }

        try {
            $all = [];
            Spell::select('id', 'name')->orderBy('id')->chunk(1000, function ($rows) use (&$all) {
                foreach ($rows as $r) {
                    $all[$r->id] = $r->name;
                }
            });

            Cache::forever('all_spells', $all);
        } finally {
            try { $lock->release(); } catch (\Throwable $e) {}
        }
    }
}
