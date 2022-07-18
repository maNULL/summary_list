<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SummaryListRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SummaryListRepository::class)]
class SummaryList extends CommonList
{
}
