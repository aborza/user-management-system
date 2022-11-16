<?php

namespace App\ExternalApi\Factory;

use App\ExternalApi\Model\User\Filter as UserFilter;
use Symfony\Component\HttpFoundation\Request;

class UserFilterFactory
{
    public function createModel(Request $request): UserFilter
    {
        return new UserFilter(
            $request->get('email'),
            $request->get('firstName'),
            $request->get('lastName')
        );
    }
}
