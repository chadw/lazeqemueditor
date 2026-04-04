<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Checkbox extends Component
{
    public ?string $name;
    public ?string $label;
    public bool $checked;
    public $attributes;

    public function __construct(?string $name = null, ?string $label = null, ?bool $checked = null, $attributes = [])
    {
        $this->name = $name;
        $this->label = $label;
        $this->checked = (bool) $checked;
        $this->attributes = $attributes;
    }

    public function render()
    {
        return view('components.form.checkbox');
    }
}
