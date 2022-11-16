<?php

namespace App\ExternalApi\Factory;

use App\Entity\Group;
use App\ExternalApi\Model\Group\Create\Request as GroupCreateRequest;
use Carbon\CarbonImmutable;

class GroupFactory
{
    public function createEntityFromModel(GroupCreateRequest $groupCreateRequest): Group
    {
        return (new Group())->setName($groupCreateRequest->getName())
            ->setActive($groupCreateRequest->getActive() ?? Group::STATUS_ACTIVE)
            ->setCreatedAt(CarbonImmutable::now())
        ;
    }
}
