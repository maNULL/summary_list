<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SummaryRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SummaryRepository::class)]
class Summary
{
    #[ORM\Id]
    #[ORM\Column(name: 'summary_id', type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'integer')]
    private ?int $kuspId;

    #[ORM\Column(type: 'integer')]
    private ?int $departmentId;

    #[ORM\Column(type: 'integer')]
    private ?int $sectionId;

    #[ORM\Column(type: 'integer')]
    private ?int $crimeTypeId;

    #[ORM\Column(type: 'boolean')]
    private bool $includeStatistics;

    #[ORM\Column(type: 'date_immutable')]
    private ?DateTimeImmutable $includeStatisticsDate;

    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    private ?string $crimeTypeExtraInfo;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $crimeTypeAtts;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $assignedDepartment;

    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    private ?string $assignedDepartmentExtraInfo;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $creatorLastname;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $kuspNumber;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?DateTimeImmutable $registrationDate;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?DateTimeImmutable $accidentDate;

    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    private ?string $accidentAddrExtraInfo;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $accidentType;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $accidentMemo;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $takenMeasures;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Summary
    {
        $this->id = $id;

        return $this;
    }

    public function getKuspId(): ?int
    {
        return $this->kuspId;
    }

    public function setKuspId(?int $kuspId): Summary
    {
        $this->kuspId = $kuspId;

        return $this;
    }

    public function getDepartmentId(): ?int
    {
        return $this->departmentId;
    }

    public function setDepartmentId(?int $departmentId): Summary
    {
        $this->departmentId = $departmentId;

        return $this;
    }

    public function getSectionId(): ?int
    {
        return $this->sectionId;
    }

    public function setSectionId(?int $sectionId): Summary
    {
        $this->sectionId = $sectionId;

        return $this;
    }

    public function getCrimeTypeId(): ?int
    {
        return $this->crimeTypeId;
    }

    public function setCrimeTypeId(?int $crimeTypeId): Summary
    {
        $this->crimeTypeId = $crimeTypeId;

        return $this;
    }

    public function isIncludeStatistics(): bool
    {
        return $this->includeStatistics;
    }

    public function setIncludeStatistics(bool $includeStatistics): Summary
    {
        $this->includeStatistics = $includeStatistics;

        return $this;
    }

    public function getIncludeStatisticsDate(): ?DateTimeImmutable
    {
        return $this->includeStatisticsDate;
    }

    public function setIncludeStatisticsDate(?DateTimeImmutable $includeStatisticsDate): Summary
    {
        $this->includeStatisticsDate = $includeStatisticsDate;

        return $this;
    }

    public function getCrimeTypeExtraInfo(): ?string
    {
        return $this->crimeTypeExtraInfo;
    }

    public function setCrimeTypeExtraInfo(?string $crimeTypeExtraInfo): Summary
    {
        $this->crimeTypeExtraInfo = $crimeTypeExtraInfo;

        return $this;
    }

    public function getCrimeTypeAtts(): ?string
    {
        return $this->crimeTypeAtts;
    }

    public function setCrimeTypeAtts(?string $crimeTypeAtts): Summary
    {
        $this->crimeTypeAtts = $crimeTypeAtts;

        return $this;
    }

    public function getAssignedDepartment(): ?string
    {
        return $this->assignedDepartment;
    }

    public function setAssignedDepartment(?string $assignedDepartment): Summary
    {
        $this->assignedDepartment = $assignedDepartment;

        return $this;
    }

    public function getAssignedDepartmentExtraInfo(): ?string
    {
        return $this->assignedDepartmentExtraInfo;
    }

    public function setAssignedDepartmentExtraInfo(?string $assignedDepartmentExtraInfo): Summary
    {
        $this->assignedDepartmentExtraInfo = $assignedDepartmentExtraInfo;

        return $this;
    }

    public function getCreatorLastname(): ?string
    {
        return $this->creatorLastname;
    }

    public function setCreatorLastname(?string $creatorLastname): Summary
    {
        $this->creatorLastname = $creatorLastname;

        return $this;
    }

    public function getKuspNumber(): ?int
    {
        return $this->kuspNumber;
    }

    public function setKuspNumber(?int $kuspNumber): Summary
    {
        $this->kuspNumber = $kuspNumber;

        return $this;
    }

    public function getRegistrationDate(): ?DateTimeImmutable
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(?DateTimeImmutable $registrationDate): Summary
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function getAccidentDate(): ?DateTimeImmutable
    {
        return $this->accidentDate;
    }

    public function setAccidentDate(?DateTimeImmutable $accidentDate): Summary
    {
        $this->accidentDate = $accidentDate;

        return $this;
    }

    public function getAccidentAddrExtraInfo(): ?string
    {
        return $this->accidentAddrExtraInfo;
    }

    public function setAccidentAddrExtraInfo(?string $accidentAddrExtraInfo): Summary
    {
        $this->accidentAddrExtraInfo = $accidentAddrExtraInfo;

        return $this;
    }

    public function getAccidentType(): ?string
    {
        return $this->accidentType;
    }

    public function setAccidentType(?string $accidentType): Summary
    {
        $this->accidentType = $accidentType;

        return $this;
    }

    public function getAccidentMemo(): ?string
    {
        return $this->accidentMemo;
    }

    public function setAccidentMemo(?string $accidentMemo): Summary
    {
        $this->accidentMemo = $accidentMemo;

        return $this;
    }

    public function getTakenMeasures(): ?string
    {
        return $this->takenMeasures;
    }

    public function setTakenMeasures(?string $takenMeasures): Summary
    {
        $this->takenMeasures = $takenMeasures;

        return $this;
    }
}
