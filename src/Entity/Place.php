<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PlaceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlaceRepository::class)]
class Place
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    public readonly string $fiasGuid;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 8)]
    public readonly string $latitude;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 8)]
    public readonly string $longitude;

    public function __construct(string $fiasGuid, string $latitude, string $longitude)
    {
        $this->fiasGuid  = $fiasGuid;
        $this->latitude  = $latitude;
        $this->longitude = $longitude;
    }
}
