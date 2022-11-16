<?php

namespace App\ExternalApi\Model\User\Create;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class Request
{
    /**
     * @Assert\Email
     */
    private ?string $email;

    /**
     * @Assert\Regex(
     *     pattern="/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/",
     *     message="Invalid password"
     * )
     * @Assert\NotBlank
     */
    private ?string $password;

    /**
     * @Assert\NotBlank
     */
    private ?string $firstName;

    /**
     * @Assert\NotBlank
     */
    private ?string $lastName;

    /**
     * @Assert\Choice(choices=User::VALID_USER_ROLES)
     */
    private ?string $role;

    /**
     * @Assert\NotBlank
     */
    private ?int $active;

    public function __construct(
        ?string $email,
        ?string $password,
        ?string $firstName,
        ?string $lastName,
        ?string $role,
        ?int    $active
    )
    {

        $this->email = $email;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->role = $role;
        $this->active = $active;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function getActive(): ?int
    {
        return $this->active;
    }
}