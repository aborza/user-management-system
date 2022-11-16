<?php

namespace App\ExternalApi\Model\User\Index;

class Request
{
    private int $offset;
    private int $limit;

    public function __construct(int $offset=0, int $limit=10)
    {
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
