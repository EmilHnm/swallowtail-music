<?php

namespace App\Orchid\Selection;

use Orchid\Screen\Layouts\Selection;

class DynamicSelection extends Selection
{

    public $template = self::TEMPLATE_LINE;

    public function __construct(
        protected array $_filters = [],
    )
    {
    }

    public function filters(): iterable
    {
        return $this->_filters;
    }
}