<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Filmecard extends Component
{
     
    public $filme;
    public $campos;
    public function __construct($filme, $campos= [])
    {
        $this->filme = $filme;
        $this->campos = is_array($campos) ? $campos : explode(',', $campos);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.filmecard');
    }
}
