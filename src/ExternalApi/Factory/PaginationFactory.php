<?php

namespace App\ExternalApi\Factory;

use App\ExternalApi\Model\Pagination;

class PaginationFactory
{
    public function createModel(array $results, int $total): Pagination
    {
        return new Pagination($results, $total);
    }
}
