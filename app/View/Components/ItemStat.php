<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ItemStat extends Component
{
    public string $name;
    public mixed $stat;
    public int|float $stat2;

    public function __construct($name, $stat, $stat2 = 0)
    {
        $this->name = $name;
        $this->stat = $stat;
        $this->stat2 = is_numeric($stat2) ? (float)$stat2 : 0;
    }

    public function render(): View|Closure|string
    {
        return view('components.item-stat');
    }
}
