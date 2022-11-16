<?php

namespace App\ExternalApi\Factory;

use App\Entity\User;
use App\ExternalApi\Model\User\Create\Request as UserCreateRequest;
use App\ExternalApi\Model\User\User as UserModel;
use Carbon\CarbonImmutable;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {

        $this->passwordHasher = $passwordHasher;
    }

    public function createEntityFromModel(UserCreateRequest $userCreateRequest): User
    {
        $user = (new User())
            ->setEmail($userCreateRequest->getEmail())
            ->setFirstName($userCreateRequest->getFirstName())
            ->setLastName($userCreateRequest->getLastName())
            ->setRole($userCreateRequest->getRole())
            ->setActive($userCreateRequest->getActive())
            ->setCreatedAt(CarbonImmutable::now())
        ;

        return $user->setPassword($this->passwordHasher->hashPassword($user, $userCreateRequest->getPassword()));
    }

    public function createModelFromEntity(User $user):UserModel
    {
        return new UserModel(
            $user->getId(),
            $user->getEmail(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getActive(),
            $user->getCreatedAt()
        );
    }
}
