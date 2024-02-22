<?php

namespace App\Models\Es;


use Spatie\LaravelData\Data;

class Number extends Data
{
    public function __construct(
        public int $value = 0,
        public string $relation = 'eq',
    )
    {
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
