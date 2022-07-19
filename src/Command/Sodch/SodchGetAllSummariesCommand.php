<?php

declare(strict_types=1);

namespace App\Command\Sodch;

use App\Entity\CurrentSummaryList;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Throwable;

#[AsCommand(
    name       : 'app:sodch:get-all-summaries',
    description: 'Get all summary list from SODCH service',
    hidden     : true
)]
class SodchGetAllSummariesCommand extends Command
{
    public function __construct(
        private readonly ClientInterface        $sodchClient,
        private readonly ParameterBagInterface  $parameterBag,
        private readonly EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
    }

    protected function configure(): void {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $summaryDate = \DateTime::createFromFormat('Y-m-d', $this->parameterBag->get('sodch_get_summary_list_from'));

        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();

        try {
            $statement = $connection->prepare('delete from current_summary_list where SUMMARY_ID is not null');
            $statement->executeStatement();
            $connection->commit();
        } catch (Throwable $e) {
            $connection->rollBack();
        }

        while ($summaryDate < (new DateTimeImmutable('previous day'))) {
            try {
                $this->saveCurrentSummaryList(
                    $this->getSummaryListByDate(new DateTimeImmutable($summaryDate->format('c')))
                );
            } catch (Throwable $e) {
                dump($e);
                $io->error($e->getMessage());

                return Command::FAILURE;
            }

            $summaryDate->modify('+1 day');
        }

        try {
            $connection->beginTransaction();

            $statement = $connection->prepare(
                'delete from SUMMARY_LIST where SUMMARY_ID in (select summary_id from summaries_diff)'
            );
            $statement->executeStatement();

            $connection->commit();
        } catch (Throwable $e) {
            $connection->rollBack();
        }

        return Command::SUCCESS;
    }

    /**
     * @throws \Exception
     */
    private function saveCurrentSummaryList(string $content)
    {
        $json = json_decode($content, true);

        if (isset($json['data']) && isset($json['data']['summaryList'])) {
            foreach ($json['data']['summaryList'] as $list) {
                $decisionDate = null;

                if (! is_null($list['decisionDate'])) {
                    $decisionDate = new DateTimeImmutable($list['decisionDate']);
                }

                $summaryList = new CurrentSummaryList();
                $summaryList
                    ->setSummaryId($list['summaryId'])
                    ->setKuspNumber($list['kuspNumber'])
                    ->setSummaryId($list['summaryId'])
                    ->setKuspNumber($list['kuspNumber'])
                    ->setTransferDate($this->unixEpochTimeConverter($list['transferDate']))
                    ->setAccidentType($list['accidentType'])
                    ->setCreateDepartment($list['createDepartment'])
                    ->setDecisionDate($decisionDate)
                    ->setCrimeType($list['crimeType'])
                    ->setAccidentAddress($list['accidentAddress'])
                    ->setComplainantFullName($list['complainantFullName'])
                    ->setCriminalCode($list['criminalCode'])
                    ->setAccidentStartDate($this->unixEpochTimeConverter($list['accidentStartDate']))
                    ->setSeverity($list['severity'])
                    ->setDecision($list['decision'])
                    ->setSummarySection($list['summarySection'])
                    ->setDisclosureUnit($list['disclosureUnit'])
                    ->setDisclosure($list['disclosure'])
                    ->setRegisteredDepartment($list['registeredDepartment'])
                    ->setCaseNumber($list['caseNumber'])
                    ->setSearchInitiator((string) $list['searchInitiator']);

                $this->entityManager->persist($summaryList);
            }

            $this->entityManager->flush();

            foreach ($json['data']['summaryList'] as $list) {
                $connection = $this->entityManager->getConnection();
                $statement  = $connection->prepare(
                    'update current_summary_list
                        set accident_memo = :accidentMemo,
                            taken_measures = :takenMeasures
                     where summary_id = :summaryId'
                );

                $accidentMemo  = (string) $list['accidentMemo'];
                $takenMeasures = (string) $list['takenMeasures'];

                $statement->bindParam('summaryId', $list['summaryId']);
                $statement->bindParam('accidentMemo', $accidentMemo, length: strlen($accidentMemo));
                $statement->bindParam('takenMeasures', $takenMeasures, length: strlen($takenMeasures));

                try {
                    $statement->executeStatement();
                } catch (Throwable $e) {
                    dump($e);
                    throw new \Exception('Ошибка вставки данных в CurrentSummaryList');
                }
            }
        }
    }

    private function unixEpochTimeConverter(int|string|null $unixTime): ?DateTimeImmutable
    {
        if (is_null($unixTime)) {
            return null;
        }

        return (\DateTimeImmutable::createFromFormat(
            'U',
            (string) intval($unixTime / 1000)
        ));
    }

    /**
     * @throws GuzzleException
     */
    private function getSummaryListByDate(\DateTimeInterface $summaryDate): string
    {
        $requestDate = $summaryDate->modify('midnight');

        $params = [
            "startCreationDate"   => $requestDate->format('c'),
            "endCreationDate"     => $requestDate->modify('+1 day -1 second')->format('c'),
            "departmentId"        => -260001,
            "withChildDepartment" => false,
        ];

        return $this
            ->sodchClient
            ->post(
                '/mvd-server/summarynew/search',
                [
                    'form_params' => [
                        'summary' => json_encode($params),
                    ],
                ]
            )
            ->getBody()
            ->getContents();
    }
}
