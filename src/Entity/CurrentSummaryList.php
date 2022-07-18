<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CurrentSummaryListRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrentSummaryListRepository::class)]
class CurrentSummaryList extends CommonList
{
}
