<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Textarea extends Component
{
    public function __construct(
        public string $name,
        public string $label,
        public mixed $value = null
    ) {}

    public function render()
    {
        return view('components.form.textarea');
    }
}
