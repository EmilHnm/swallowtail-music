<?php

namespace App\Models\Es;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class RawHits extends Data
{
    public function __construct(
        public Number $total,
        public ?float $max_score = null,
        #[DataCollectionOf(Record::class)]
        public ?DataCollection $hits = null,
    )
    {
    }
}