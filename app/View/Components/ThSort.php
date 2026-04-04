<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ThSort extends Component
{
    public string $field;
    public string $label;
    public string $class;

    public function __construct(string $field, string $label, string $class = '')
    {
        $this->field = $field;
        $this->label = $label;
        $this->class = $class;
    }

    public function render(): View|Closure|string
    {
        return view('components.th-sort');
    }

    public function isActive(): bool
    {
        return request('sort') === $this->field;
    }

    public function direction(): string
    {
        return request('direction', 'asc');
    }

    public function nextDirection(): string
    {
        if ($this->isActive() && $this->direction() === 'asc') {
            return 'desc';
        }
        return 'asc';
    }

    public function url(): string
    {
        return request()->fullUrlWithQuery([
            'sort' => $this->field,
            'direction' => $this->nextDirection(),
            'page' => 1,
        ]);
    }
}
