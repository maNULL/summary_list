<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\MapElementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MapElementRepository::class, readOnly: true)]
class MapElement
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    public readonly int $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public readonly string $type;

    #[ORM\Column(type: 'text', nullable: true)]
    public readonly string $memo;

    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    public readonly string $address;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    public readonly string $latitude;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 7, nullable: true)]
    public readonly string $longitude;
}
