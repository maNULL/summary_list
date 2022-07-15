<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CurrentSummaryListRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrentSummaryListRepository::class)]
class CurrentSummaryList
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private ?int $summaryId;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $kuspNumber;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $transferDate;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $accidentType;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $createDepartment;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?DateTimeImmutable $decisionDate;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $crimeType;

    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    private ?string $accidentAddress;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $complainantFullName;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $criminalCode;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $accidentStartDate;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $severity;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $decision;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $summarySection;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $disclosureUnit;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $disclosure;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $registeredDepartment;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $caseNumber;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $searchInitiator;

    #[ORM\Column(type: 'text')]
    private ?string $accidentMemo;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $takenMeasures;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getKuspNumber(): ?int
    {
        return $this->kuspNumber;
    }

    public function setKuspNumber(?int $kuspNumber): self
    {
        $this->kuspNumber = $kuspNumber;

        return $this;
    }

    public function getTransferDate(): ?DateTimeImmutable
    {
        return $this->transferDate;
    }

    public function setTransferDate(DateTimeImmutable $transferDate): self
    {
        $this->transferDate = $transferDate;

        return $this;
    }

    public function getAccidentType(): ?string
    {
        return $this->accidentType;
    }

    public function setAccidentType(string $accidentType): self
    {
        $this->accidentType = $accidentType;

        return $this;
    }

    public function getCreateDepartment(): ?string
    {
        return $this->createDepartment;
    }

    public function setCreateDepartment(string $createDepartment): self
    {
        $this->createDepartment = $createDepartment;

        return $this;
    }

    public function getDecisionDate(): ?DateTimeImmutable
    {
        return $this->decisionDate;
    }

    public function setDecisionDate(?DateTimeImmutable $decisionDate): self
    {
        $this->decisionDate = $decisionDate;

        return $this;
    }

    public function getCrimeType(): ?string
    {
        return $this->crimeType;
    }

    public function setCrimeType(?string $crimeType): self
    {
        $this->crimeType = $crimeType;

        return $this;
    }

    public function getAccidentAddress(): ?string
    {
        return $this->accidentAddress;
    }

    public function setAccidentAddress(?string $accidentAddress): self
    {
        $this->accidentAddress = $accidentAddress;

        return $this;
    }

    public function getComplainantFullName(): ?string
    {
        return $this->complainantFullName;
    }

    public function setComplainantFullName(?string $complainantFullName): self
    {
        $this->complainantFullName = $complainantFullName;

        return $this;
    }

    public function getCriminalCode(): ?string
    {
        return $this->criminalCode;
    }

    public function setCriminalCode(?string $criminalCode): self
    {
        $this->criminalCode = $criminalCode;

        return $this;
    }

    public function getAccidentStartDate(): ?DateTimeImmutable
    {
        return $this->accidentStartDate;
    }

    public function setAccidentStartDate(?DateTimeImmutable $accidentStartDate): self
    {
        $this->accidentStartDate = $accidentStartDate;

        return $this;
    }

    public function getSeverity(): ?string
    {
        return $this->severity;
    }

    public function setSeverity(?string $severity): self
    {
        $this->severity = $severity;

        return $this;
    }

    public function getDecision(): ?string
    {
        return $this->decision;
    }

    public function setDecision(string $decision): self
    {
        $this->decision = $decision;

        return $this;
    }

    public function getSummarySection(): ?string
    {
        return $this->summarySection;
    }

    public function setSummarySection(?string $summarySection): self
    {
        $this->summarySection = $summarySection;

        return $this;
    }

    public function getDisclosureUnit(): ?string
    {
        return $this->disclosureUnit;
    }

    public function setDisclosureUnit(?string $disclosureUnit): self
    {
        $this->disclosureUnit = $disclosureUnit;

        return $this;
    }

    public function getDisclosure(): ?string
    {
        return $this->disclosure;
    }

    public function setDisclosure(?string $disclosure): self
    {
        $this->disclosure = $disclosure;

        return $this;
    }

    public function getRegisteredDepartment(): ?string
    {
        return $this->registeredDepartment;
    }

    public function setRegisteredDepartment(string $registeredDepartment): self
    {
        $this->registeredDepartment = $registeredDepartment;

        return $this;
    }

    public function getCaseNumber(): ?string
    {
        return $this->caseNumber;
    }

    public function setCaseNumber(?string $caseNumber): self
    {
        $this->caseNumber = $caseNumber;

        return $this;
    }

    public function getSearchInitiator(): ?string
    {
        return $this->searchInitiator;
    }

    public function setSearchInitiator(?string $searchInitiator): self
    {
        $this->searchInitiator = $searchInitiator;

        return $this;
    }

    public function getAccidentMemo(): ?string
    {
        return $this->accidentMemo;
    }

    public function setAccidentMemo(string $accidentMemo): self
    {
        $this->accidentMemo = $accidentMemo;

        return $this;
    }

    public function getTakenMeasures(): ?string
    {
        return $this->takenMeasures;
    }

    public function setTakenMeasures(?string $takenMeasures): self
    {
        $this->takenMeasures = $takenMeasures;

        return $this;
    }
}
