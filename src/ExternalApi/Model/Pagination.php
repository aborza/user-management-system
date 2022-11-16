<?php

namespace App\ExternalApi\Model;

use JsonSerializable;

class Pagination implements JsonSerializable
{
    private array $results;
    private int $total;

    public function __construct(array $results, int $total)
    {
        $this->results = $results;
        $this->total = $total;
    }

    public function jsonSerialize()
    {
        return [
            'results' => $this->results,
            'total' => $this->total
        ];
    }
}
