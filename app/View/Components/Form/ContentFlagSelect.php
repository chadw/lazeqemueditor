<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;
use App\Models\ContentFlag;

class ContentFlagSelect extends Component
{
    public $name;
    public $label;
    public $flags;
    public $selected;

    /**
     * Create a new component instance.
     *
     * @param string|null $name
     * @param string|null $label
     * @param mixed $selected
     */
    public function __construct($name = 'content_flags', $label = null, $selected = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->selected = $selected;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        // Explicitly pass flags to the view so the blade never needs to query.
        return view('components.form.content-flag-select');
    }
}
