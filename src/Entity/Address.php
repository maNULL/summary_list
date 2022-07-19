<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\Column(name: 'address_id', type: 'integer')]
    private ?int $id;

    #[ORM\Column(name: 'address_text', type: 'string', length: 1000, nullable: true)]
    private ?string $text;

    #[ORM\Column(type: 'guid', nullable: true)]
    private ?string $fiasGuid;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $aptNumber;

    #[ORM\Column(type: 'boolean')]
    private bool $house;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Address
    {
        $this->id = $id;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): Address
    {
        $this->text = $text;

        return $this;
    }

    public function getFiasGuid(): ?string
    {
        return $this->fiasGuid;
    }

    public function setFiasGuid(?string $fiasGuid): Address
    {
        $this->fiasGuid = $fiasGuid;

        return $this;
    }

    public function getAptNumber(): ?string
    {
        return $this->aptNumber;
    }

    public function setAptNumber(?string $aptNumber): Address
    {
        $this->aptNumber = $aptNumber;

        return $this;
    }

    public function isHouse(): bool
    {
        return $this->house;
    }

    public function setHouse(bool $house): Address
    {
        $this->house = $house;

        return $this;
    }
}
