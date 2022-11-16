<?php

namespace App\ExternalApi\Model\Group\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Request
{
    /**
     * @Assert\NotBlank
     */
    private string $name;

    private ?int $active;

    public function __construct(string $name, ?int $active)
    {
        $this->name = $name;
        $this->active = $active;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getActive(): ?int
    {
        return $this->active;
    }
}