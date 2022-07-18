<?php

declare(strict_types=1);

namespace App\Command\Sodch;

use App\Entity\Address;
use App\Entity\Person;
use App\Entity\Place;
use App\Entity\Summary;
use App\Repository\AddressRepository;
use App\Repository\PlaceRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name       : 'app:sodch:get-summary-by-id',
    description: 'Get summary by ID from SODCH service',
    hidden     : true
)]
class SodchGetSummaryByIdCommand extends Command
{
    public function __construct(
        private readonly ClientInterface        $sodchClient,
        private readonly EntityManagerInterface $entityManager,
        private readonly PlaceRepository        $placeRepository,
        private readonly AddressRepository      $addressRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'Summary ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $id = $input->getArgument('id');

        $response = $this->sodchClient->get(
            sprintf('/mvd-server/summarynew/%s', $id),
            [
                'query' => ['_dc' => round(microtime(true) * 1000)],
            ]
        );

        if ($response->getStatusCode() === 200) {
            $summary = json_decode($response->getBody()->getContents());

            if ($summary->success) {
                $this->saveSummary((array) $summary->data);
            }
        }

        return Command::SUCCESS;
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    private function saveSummary(array $data): void
    {
        //dump($data);

        try {
            $summary = new Summary();
            $summary
                ->setId($data['summaryId'])
                ->setKuspId($data['kuspId'])
                ->setDepartmentId($data['departmentId'])
                ->setSectionId($data['sectionId'])
                ->setCrimeTypeId($data['crimeTypeId'])
                ->setIncludeStatistics($data['includeStatistics'])
                ->setIncludeStatisticsDate(
                    $data['includeStatisticsDate'] === null
                        ? null
                        : new DateTimeImmutable($data['includeStatisticsDate'])
                )
                ->setCrimeTypeExtraInfo($data['crimeTypeExtraInfo'])
                ->setCrimeTypeAtts($data['crimeTypeAtts'])
                ->setAssignedDepartment($data['assignedDepartment'])
                ->setAssignedDepartmentExtraInfo($data['assignedDepartmentExtraInfo'])
                ->setCreatorLastname($data['creatorLastName'])
                ->setKuspNumber($data['kuspNumber'])
                ->setRegistrationDate($this->unixEpochTimeConverter($data['registrationDate']))
                ->setAccidentDate($this->unixEpochTimeConverter($data['accidentDate']))
                ->setAccidentAddrExtraInfo($data['accidentAddrExtraInfo'])
                ->setAccidentType($data['accidentType']);

            if (! is_null($data['accidentAddress'])) {
                $aa = (array) $data['accidentAddress'];

                $summary->setAccidentAddress($this->getAddress($aa));

                $place = $this->placeRepository->find($aa['fiasGuid']);

                if ($place === null) {
                    $coordinate = $this->getCoordinateByFiasGuid($aa['fiasGuid']);

                    if ($coordinate !== null) {
                        $place = new Place($aa['fiasGuid'], ...$coordinate);
                        $this->entityManager->persist($place);
                    }
                }
            }

            $this->entityManager->persist($summary);

            foreach ($data['persons'] as $p) {
                $p = (array) $p;

                $person = new Person();
                $person
                    ->setId($p['personId'])
                    ->setLastName($p['lastName'])
                    ->setFirstName($p['firstName'])
                    ->setMiddleName($p['middleName'])
                    ->setBirthDate(
                        $p['birthDate'] === null
                            ? null
                            : DateTimeImmutable::createFromFormat('d.m.Y', $p['birthDate'])
                    );

                if (! is_null($p['address'])) {
                    $a = (array) $p['address'];

                    $person->setAddress($this->getAddress($a));
                }

                $summary->addPerson($person);
            }

            $this->entityManager->flush();

            $connection = $this->entityManager->getConnection();

            $statement = $connection->prepare(
                'update summary
                    set accident_memo = :accidentMemo,
                        taken_measures = :takenMeasures
                    where summary_id = :summaryId'
            );

            $accidentMemo  = (string) $data['memo'];
            $takenMeasures = (string) $data['takenMeasures'];

            $statement->bindParam('summaryId', $data['summaryId']);
            $statement->bindParam('accidentMemo', $accidentMemo, length: strlen($accidentMemo));
            $statement->bindParam('takenMeasures', $takenMeasures, length: strlen($takenMeasures));

            try {
                $statement->executeStatement();
            } catch (Throwable $e) {
                dump($e);
                throw new \Exception('Ошибка вставки CLOB полей в Summary');
            }

//            $this->connection->beginTransaction();
//
//            $statement = $this->connection->prepare(
//                'INSERT INTO summary(
//                    summaryid, kuspid, departmentid, sectionid, includestatistics, includestatisticsdate, crimetypeid,
//                    crimetypeextrainfo, crimetypeatts, assigneddepartment, assigneddepartmentextrainfo, creatorlastname,
//                    takenmeasures, kuspnumber, registrationdate, accidentdate, accidentaddrextrainfo, additionalattrs,
//                    accidenttype, memo)
//                values (:summaryId, :kuspId, :departmentId, :sectionId, :includeStatistics, :includeStatisticsDate, :crimeTypeId,
//                    :crimeTypeExtraInfo, :crimeTypeAtts, :assignedDepartment, :assignedDepartmentExtraInfo, :creatorLastname,
//                    :takenMeasures, :kuspNumber, :registrationDate, :accidentDate, :accidentAddrExtraInfo, :additionalAttrs,
//                    :accidentType, :memo)'
//            );
//            $statement->bindParam('summaryId', $summary['summaryId'], ParameterType::INTEGER);
//            $statement->bindParam('kuspId', $summary['kuspId'], ParameterType::INTEGER);
//            $statement->bindParam('departmentId', $summary['departmentId'], ParameterType::INTEGER);
//            $statement->bindParam('sectionId', $summary['sectionId'], ParameterType::INTEGER);
//            $statement->bindParam('includeStatistics', $summary['includeStatistics']);
//            $statement->bindParam('includeStatisticsDate', $summary['includeStatisticsDate']);
//            $statement->bindParam('crimeTypeId', $summary['crimeTypeId'], ParameterType::INTEGER);
//            $statement->bindParam('crimeTypeExtraInfo', $summary['crimeTypeExtraInfo']);
//            $statement->bindParam('crimeTypeAtts', $summary['crimeTypeAtts']);
//            $statement->bindParam('assignedDepartment', $summary['assignedDepartment']);
//            $statement->bindParam('assignedDepartmentExtraInfo', $summary['assignedDepartmentExtraInfo']);
//            $statement->bindParam('creatorLastname', $summary['creatorLastname']);
//            $statement->bindParam('takenMeasures', $summary['takenMeasures']);
//            $statement->bindParam('kuspNumber', $summary['kuspNumber']);
//            $statement->bindValue('registrationDate', $this->unixEpochTimeConverter($summary['registrationDate']));
//            $statement->bindValue('accidentDate', $this->unixEpochTimeConverter($summary['accidentDate']));
//            $statement->bindParam('accidentAddrExtraInfo', $summary['accidentAddrExtraInfo']);
//            $statement->bindParam('additionalAttrs', $summary['additionalAttrs']);
//            $statement->bindParam('accidentType', $summary['accidentType']);
//
//            $memo = (string) $summary['memo'];
//            $statement->bindParam('memo', $memo, length: strlen($memo));
//
//            try {
//                $statement->executeStatement();
//            } catch (\Throwable $e) {
//                dump($e->getMessage());
//                throw new \Exception('Ошибка вставки данных в SUMMARY');
//            }
//            if (array_key_exists('persons', $summary) && count($summary['persons']) > 0) {
//                $persons = $summary['persons'];
//
//                $statement = $this->connection->prepare(
//                    '
//                    INSERT INTO SUMMARYPERSONS(
//                       SUMMARYID, PERSONID, LASTNAME, FIRSTNAME, MIDDLENAME, BIRTHDATE,
//                       PREVENTIVEMEASURES, ADDRESSTEXT, ISFOREIGN, PREVENTIVEMEASURETYPE)
//                    VALUES (
//                        :summaryId, :personId, :lastName, :firstName, :middleName, :birthDate,
//                        :preventiveMeasures, :address, :isForeign, :preventiveMeasureType)
//                '
//                );
//
//                foreach ($persons as $person) {
//                    $person = (array) $person;
//
//                    $statement->bindParam('summaryId', $summary['summaryId'], ParameterType::INTEGER);
//                    $statement->bindParam('personId', $person['personId'], ParameterType::INTEGER);
//                    $statement->bindParam('lastName', $person['lastName']);
//                    $statement->bindParam('firstName', $person['firstName']);
//                    $statement->bindParam('middleName', $person['middleName']);
//
//                    $statement->bindValue(
//                        'birthDate',
//                        (\DateTimeImmutable::createFromFormat('d.m.Y', $person['birthDate']))->format('Y-m-d')
//                    );
//
//                    $statement->bindParam('preventiveMeasures', $person['preventiveMeasures']);
//                    $statement->bindParam('address', $person['address']);
//                    $statement->bindParam('isForeign', $person['isForeign']);
//                    $statement->bindParam('preventiveMeasureType', $person['preventiveMeasureType']);
//
//                    try {
//                        $statement->executeStatement();
//                    } catch (\Throwable $e) {
//                        dump($e->getMessage());
//                        throw new \Exception('Ошибка вставки данных в PERSONS');
//                    }
//                }
//            }
//            $this->connection->commit();
        } catch (\Throwable $e) {
            dd([$e, $data]);
            //$this->connection->rollBack();
        }
    }

    private function unixEpochTimeConverter(int|string|null $unixTime): ?DateTimeImmutable
    {
        if (is_null($unixTime)) {
            return null;
        }

        return DateTimeImmutable::createFromFormat(
            'U',
            (string) intval($unixTime / 1000)
        );
    }

    private function getAddress(array $a): ?Address
    {
        $address = $this->addressRepository->find($a['addressId']);

        if ($address === null) {
            $address = new Address();
            $address
                ->setId($a['addressId'])
                ->setText($a['addressText'])
                ->setFiasGuid($a['fiasGuid'])
                ->setAptNumber($a['aptNumber'])
                ->setHouse($a['house']);

            $this->entityManager->persist($address);
        }

        return $address;
    }

    private function getCoordinateByFiasGuid(string $fiasGuid): ?array
    {
        $response = $this->sodchClient->get(
            '/mvd-server/address/findextension',
            [
                'query' => [
                    '_dc'      => round(microtime(true) * 1000),
                    'fiasGuid' => $fiasGuid,
                ],
            ]
        );

        $place = (array) json_decode($response->getBody()->getContents());

        if (array_key_exists('data', $place)) {
            $data = (array) $place['data'];

            if ($data['latitude'] === null || $data['longitude'] === null) {
                return null;
            }

            return [
                'latitude'  => $data['latitude'],
                'longitude' => $data['longitude'],
            ];
        }

        return null;
    }
}
