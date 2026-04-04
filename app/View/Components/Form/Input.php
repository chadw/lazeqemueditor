<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Input extends Component
{
    public function __construct(
        public string $name,
        public string $label = '',
        public string $type = 'text',
        public mixed $value = null,
        public bool $required = false
    ) {}

    public function render()
    {
        return view('components.form.input');
    }
}
