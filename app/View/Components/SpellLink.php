<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SpellLink extends Component
{
    public $spellId;
    public $spellName;
    public $spellIcon;
    public $spellTargetType;
    public string $spellClass;
    public string $linkClass;
    public bool $effectsOnly;
    public bool $iconOnly;

    public function __construct(
        int $spellId,
        string|null $spellName = null,
        $spellIcon = null,
        string $spellClass = '',
        string $linkClass = 'link-info link-hover flex items-center gap-1',
        int|null $spellTargetType = null,
        bool $iconOnly = false,
        bool $effectsOnly = false
    ) {
        $this->spellId = $spellId;
        $this->spellName = $spellName;
        $this->spellIcon = $spellIcon;
        $this->spellClass = $spellClass;
        $this->linkClass = $linkClass;
        $this->spellTargetType = $spellTargetType;
        $this->iconOnly = $iconOnly;
        $this->effectsOnly = $effectsOnly;
    }

    public function render(): View|Closure|string
    {
        return view('components.spell-link');
    }
}
