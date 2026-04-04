<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;
use Illuminate\Support\Collection;

class MultiSelect extends Component
{
    public function __construct(
        public string $name,
        public ?string $label = null,
        public iterable $options,
        public iterable|null $selected = null,
        public string|null $placeholder = null,
    ) {}

    public function normalizedSelected(): array
    {
        if (is_null($this->selected)) {
            return [];
        }

        if (is_string($this->selected)) {
            return array_filter(explode('|', $this->selected));
        }

        if ($this->selected instanceof Collection) {
            return $this->selected->toArray();
        }

        return (array) $this->selected;
    }

    public function render()
    {
        return view('components.form.multi-select');
    }
}
