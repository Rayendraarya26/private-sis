<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Breadcrumbs extends Component
{
    public array $dataBreadcrumbs;

    public function __construct($data)
    {
        $this->dataBreadcrumbs = $data;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.breadcrumbs')->with(['breadcrumbs' => $this->dataBreadcrumbs]);
    }
}
