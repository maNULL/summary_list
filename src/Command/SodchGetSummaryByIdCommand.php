<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name       : 'app:sodch:get-summary-by-id',
    description: 'Get summary by ID from SODCH service',
    hidden     : true
)]
class SodchGetSummaryByIdCommand extends Command
{
    public function __construct(
        private readonly ClientInterface $sodchClient,
        private readonly Connection      $connection
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

        $currentId = $input->getArgument('id');

        $response = $this->sodchClient->get(
            sprintf('/mvd-server/summarynew/%s', $currentId),
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
    private function saveSummary(array $summary): void
    {
//        dd($summary);
        try {
            $this->connection->beginTransaction();

            $statement = $this->connection->prepare(
                'INSERT INTO summary(
                    summaryid, kuspid, departmentid, sectionid, includestatistics, includestatisticsdate, crimetypeid,
                    crimetypeextrainfo, crimetypeatts, assigneddepartment, assigneddepartmentextrainfo, creatorlastname,
                    takenmeasures, kuspnumber, registrationdate, accidentdate, accidentaddrextrainfo, additionalattrs,
                    accidenttype, memo)
                values (:summaryId, :kuspId, :departmentId, :sectionId, :includeStatistics, :includeStatisticsDate, :crimeTypeId,
                    :crimeTypeExtraInfo, :crimeTypeAtts, :assignedDepartment, :assignedDepartmentExtraInfo, :creatorLastname,
                    :takenMeasures, :kuspNumber, :registrationDate, :accidentDate, :accidentAddrExtraInfo, :additionalAttrs,
                    :accidentType, :memo)'
            );
            $statement->bindParam('summaryId', $summary['summaryId'], ParameterType::INTEGER);
            $statement->bindParam('kuspId', $summary['kuspId'], ParameterType::INTEGER);
            $statement->bindParam('departmentId', $summary['departmentId'], ParameterType::INTEGER);
            $statement->bindParam('sectionId', $summary['sectionId'], ParameterType::INTEGER);
            $statement->bindParam('includeStatistics', $summary['includeStatistics']);
            $statement->bindParam('includeStatisticsDate', $summary['includeStatisticsDate']);
            $statement->bindParam('crimeTypeId', $summary['crimeTypeId'], ParameterType::INTEGER);
            $statement->bindParam('crimeTypeExtraInfo', $summary['crimeTypeExtraInfo']);
            $statement->bindParam('crimeTypeAtts', $summary['crimeTypeAtts']);
            $statement->bindParam('assignedDepartment', $summary['assignedDepartment']);
            $statement->bindParam('assignedDepartmentExtraInfo', $summary['assignedDepartmentExtraInfo']);
            $statement->bindParam('creatorLastname', $summary['creatorLastname']);
            $statement->bindParam('takenMeasures', $summary['takenMeasures']);
            $statement->bindParam('kuspNumber', $summary['kuspNumber']);
            $statement->bindValue('registrationDate', $this->unixEpochTimeConverter($summary['registrationDate']));
            $statement->bindValue('accidentDate', $this->unixEpochTimeConverter($summary['accidentDate']));
            $statement->bindParam('accidentAddrExtraInfo', $summary['accidentAddrExtraInfo']);
            $statement->bindParam('additionalAttrs', $summary['additionalAttrs']);
            $statement->bindParam('accidentType', $summary['accidentType']);

            $memo = (string) $summary['memo'];
            $statement->bindParam('memo', $memo, length: strlen($memo));

            try {
                $statement->executeStatement();
            } catch (\Throwable $e) {
                dump($e->getMessage());
                throw new \Exception('Ошибка вставки данных в SUMMARY');
            }
            if (array_key_exists('persons', $summary) && count($summary['persons']) > 0) {
                $persons = $summary['persons'];

                $statement = $this->connection->prepare(
                    '
                    INSERT INTO SUMMARYPERSONS(
                       SUMMARYID, PERSONID, LASTNAME, FIRSTNAME, MIDDLENAME, BIRTHDATE,
                       PREVENTIVEMEASURES, ADDRESSTEXT, ISFOREIGN, PREVENTIVEMEASURETYPE)
                    VALUES (
                        :summaryId, :personId, :lastName, :firstName, :middleName, :birthDate, 
                        :preventiveMeasures, :address, :isForeign, :preventiveMeasureType)
                '
                );

                foreach ($persons as $person) {
                    $person = (array) $person;

                    $statement->bindParam('summaryId', $summary['summaryId'], ParameterType::INTEGER);
                    $statement->bindParam('personId', $person['personId'], ParameterType::INTEGER);
                    $statement->bindParam('lastName', $person['lastName']);
                    $statement->bindParam('firstName', $person['firstName']);
                    $statement->bindParam('middleName', $person['middleName']);

                    $statement->bindValue(
                        'birthDate',
                        (\DateTimeImmutable::createFromFormat('d.m.Y', $person['birthDate']))->format('Y-m-d')
                    );

                    $statement->bindParam('preventiveMeasures', $person['preventiveMeasures']);
                    $statement->bindParam('address', $person['address']);
                    $statement->bindParam('isForeign', $person['isForeign']);
                    $statement->bindParam('preventiveMeasureType', $person['preventiveMeasureType']);

                    try {
                        $statement->executeStatement();
                    } catch (\Throwable $e) {
                        dump($e->getMessage());
                        throw new \Exception('Ошибка вставки данных в PERSONS');
                    }
                }
            }
            $this->connection->commit();
        } catch (\Throwable $e) {
            dump($e->getMessage());
            dump($summary);
            $this->connection->rollBack();
        }
    }

    private function unixEpochTimeConverter(int|string|null $unixTime, string $format = 'Y-m-d H:i:s'): ?string
    {
        if (is_null($unixTime)) {
            return null;
        }

        return (\DateTimeImmutable::createFromFormat(
            'U',
            (string) intval($unixTime / 1000)
        ))->format($format);
    }
}
