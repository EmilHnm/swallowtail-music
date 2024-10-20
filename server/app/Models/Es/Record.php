<?php

namespace App\Models\Es;

use App\Models\Ds\RawDocument;
use App\Models\Institution;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;

class Record extends Data
{
    public function __construct(
        public string $_type = '',
        public string $_id = '',
        public float|string $_score = '',
        public string $_routing = '',
        public array  $_source = [],
        public string $__class_name = '',
    )
    {
        $this->__class_name = $this->_source['__class_name'] ?? '';
    }

    public function toModel(): ?Model
    {
        if(!class_exists($this->__class_name)){
            return null;
        }
        return (new $this->__class_name)->newFromBuilder($this->_source);
    }

}