<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CrimeTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CrimeTypeRepository::class, readOnly: true)]
class CrimeType
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['comment' => 'УИ записи'])]
    public readonly int $id;

    #[ORM\Column(type: 'string', length: 255, options: ['comment' => 'Наименование типа происшествия'])]
    public readonly string $title;

    #[ORM\Column(type: 'string', length: 30, options: ['comment' => 'Цвет иконки для отображения на интерфейсе'])]
    public readonly string $markerColor;

    #[ORM\Column(type: 'string', length: 100, options: ['comment' => 'Классы иконок FontAwesome'])]
    public readonly string $markerIcon;
}
