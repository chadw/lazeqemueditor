<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Select extends Component
{
    public function __construct(
        public string $name,
        public string $label = '',
        public array $options = [],
        public mixed $selected = null
    ) {}

    public function render()
    {
        return view('components.form.select');
    }
}
