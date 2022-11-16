<?php

namespace App\ExternalApi\Model\User;

use DateTimeImmutable;

class User
{
    private int $id;
    private string $email;
    private string $firstName;
    private string $lastName;
    private int $active;
    private DateTimeImmutable $createdAt;

    public function __construct(
        int               $id,
        string            $email,
        string            $firstName,
        string            $lastName,
        int               $active,
        DateTimeImmutable $createdAt
    )
    {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->active = $active;
        $this->createdAt = $createdAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getActive(): int
    {
        return $this->active;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}