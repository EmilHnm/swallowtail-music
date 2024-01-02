<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Layout;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Repository;

class DumpModal extends Modal
{

    public function __construct(string $key, array $layouts = [], $target = 'model')
    {
        $layouts = [
            new class($target) extends Layout {
                public function __construct(protected $target)
                {
                }

                public function build(Repository $repository)
                {
                    $data = $repository->getContent($this->target);
                    return view('vendor.platform.dump', ['data' => $data]);
                }
            },
        ];
        parent::__construct($key, $layouts);
    }
}