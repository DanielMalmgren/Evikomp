<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Color;

class EditContent extends Component
{

    public $content;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        $data = [
            'colors' => Color::all(),
        ];

        return view('components.edit-'.$this->content->type.'-content')->with($data);
    }
}
