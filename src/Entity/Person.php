<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PersonRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
class Person
{
    #[ORM\Id]
    #[ORM\Column(name: 'person_id', type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 32)]
    private string $lastName;

    #[ORM\Column(type: 'string', length: 32)]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 32)]
    private string $middleName;

    #[ORM\Column(type: 'date_immutable')]
    private DateTimeImmutable $birthDate;

    #[ORM\ManyToOne(targetEntity: Address::class)]
    #[ORM\JoinColumn(name: 'address_id', referencedColumnName: 'id', nullable: true)]
    private ?Address $address;

    #[ORM\ManyToOne(targetEntity: Summary::class, inversedBy: 'persons')]
    #[ORM\JoinColumn(name: 'summary_id', referencedColumnName: 'id', nullable: false)]
    private ?Summary $summary;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    public function setMiddleName(string $middleName): self
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getBirthDate(): DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(DateTimeImmutable $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getSummary(): ?Summary
    {
        return $this->summary;
    }

    public function setSummary(Summary $summary): self
    {
        $this->summary = $summary;

        return $this;
    }
}
