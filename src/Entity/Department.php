<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DepartmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepartmentRepository::class, readOnly: true)]
class Department
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    public readonly int $id;

    #[ORM\Column(type: 'string', length: 500)]
    public readonly string $name;

    #[ORM\Column(type: 'string', length: 255)]
    public readonly string $shortName;

    #[ORM\Column(type: 'string', length: 255)]
    public readonly string $extraShortName;
}
