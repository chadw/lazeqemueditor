<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PetLink extends Component
{
    public $petId;
    public $petName;
    public string $petClass;
    public bool $effectsOnly;

    public function __construct(
        int $petId,
        string|null $petName = null,
        string $petClass = '',
        bool $effectsOnly = false
    ) {
        $this->petId = $petId;
        $this->petName = $petName;
        $this->petClass = $petClass;
        $this->effectsOnly = $effectsOnly;
    }

    public function render(): View|Closure|string
    {
        return view('components.pet-link');
    }
}
