<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ItemLink extends Component
{
    public $itemId;
    public $itemName;
    public $itemIcon;
    public string $itemClass;

    public function __construct(int $itemId, string $itemName, $itemIcon = null, string $itemClass = '')
    {
        $this->itemId = $itemId;
        $this->itemName = $itemName;
        $this->itemIcon = $itemIcon;
        $this->itemClass = $itemClass;
    }

    public function render(): View|Closure|string
    {
        return view('components.item-link');
    }
}
