<?php

namespace App\Jobs;

use App\Models\AlternateCurrency;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class RefreshAltCurrencyCache implements ShouldQueue
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
            $currencies = AlternateCurrency::with('item:id,Name,icon')->get();

            Cache::forever('alt_currency', $currencies);
        } finally {
            try { $lock->release(); } catch (\Throwable $e) {}
        }
    }
}
