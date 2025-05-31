<?php

namespace App\View\Components;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\View\Component;

class SearchBox extends Component
{
    public $inputs;
    public $selects;
    public $action;
    public $method;
    public $buttonLabel;

    /**
     * Create a new component instance.
     *
     * @param array $inputs
     * @param array $selects
     * @param string $action
     * @param string $method
     * @param string $buttonLabel
     */
    public function __construct(
        array  $inputs = [],
        array  $selects = [],
        string $action = '',
        string $method = 'GET',
        string $buttonLabel = 'جستجو'
    )
    {
        $this->inputs = $inputs;
        $this->selects = $selects;
        $this->action = $action;
        $this->method = $method;
        $this->buttonLabel = $buttonLabel;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return Factory|View|Application|\Illuminate\View\View|object
     */
    public function render()
    {
        return view('components.search-box');
    }
}
