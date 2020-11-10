<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Color;

class AddContent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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

        return view('components.add-content')->with($data);
    }
}
