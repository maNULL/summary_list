<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
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
        private readonly ClientInterface       $sodchClient,
        private readonly Connection            $databaseConnection,
        private readonly ParameterBagInterface $parameterBag
    )
    {
        parent::__construct();
    }

    protected function configure(): void {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $summaryDate = \DateTime::createFromFormat('Y-m-d', $this->parameterBag->get('sodch_get_summary_list_from'));

        while ($summaryDate < (new \DateTimeImmutable('previous day'))) {
            try {
                $this->saveCurrentSummaryList(
                    $this->getSummaryListByDate(new \DateTimeImmutable($summaryDate->format('c')))
                );
            } catch (Throwable $e) {
                $io->error($e->getMessage());

                return Command::FAILURE;
            }

            $summaryDate->modify('+1 day');
        }

        return Command::SUCCESS;
    }

    /**
     * @throws \Exception
     */
    private function saveCurrentSummaryList(string $content)
    {
        $json = json_decode($content, true);

        $insertSql = '
            insert into tek_summarylist(SYMMARYID, KUSPNUMBER, TRANSFERDATE, ACCIDENTMEMO, ACCIDENTTYPE, CREATEDEPARTMENT, 
                DECISIONDATE, CRIMETYPE, ACCIDENTADDRESS, COMPLAINANTFULLNAME, CRIMINALCODE, ACCIDENTSTARTDATE,
                SEVERITY, DECISION, SUMMARYSECTION, DISCLOSUREUNIT, DESCLOSURE, TAKENMEASURES, REGISTEREDDEPARTMENT,
                CASENUMBER, SEARCHINITIOATOR)
            values (:summaryId,:kuspNumber,:transferDate,:accidentMemo,:accidentType,:createDepartment,
                    :decisionDate,:crimeType,:accidentAddress,:complainantFullName,:criminalCode,
                    :accidentStartDate,:severity,:decision,:summarySection,:disclosureUnit,:disclosure,
                    :takenMeasures,:registeredDepartment,:caseNumber,:searchInitiator)';

        $statement = $this->databaseConnection->prepare($insertSql);

        if (isset($json['data']) && isset($json['data']['summaryList'])) {
            foreach ($json['data']['summaryList'] as $list) {
                $statement->bindParam('summaryId', $list['summaryId'], ParameterType::INTEGER);
                $statement->bindParam('kuspNumber', $list['kuspNumber'], ParameterType::INTEGER);
                $statement->bindValue('transferDate', $this->unixEpochTimeConverter($list['transferDate']));
                $statement->bindParam('accidentType', $list['accidentType']);
                $statement->bindParam('createDepartment', $list['createDepartment']);
                $statement->bindParam('decisionDate', $list['decisionDate']);
                $statement->bindParam('crimeType', $list['crimeType']);
                $statement->bindParam('accidentAddress', $list['accidentAddress']);
                $statement->bindParam('complainantFullName', $list['complainantFullName']);
                $statement->bindParam('criminalCode', $list['criminalCode']);
                $statement->bindValue('accidentStartDate', $this->unixEpochTimeConverter($list['accidentStartDate']));
                $statement->bindParam('severity', $list['severity']);
                $statement->bindParam('decision', $list['decision']);
                $statement->bindParam('summarySection', $list['summarySection']);
                $statement->bindParam('disclosureUnit', $list['disclosureUnit']);
                $statement->bindParam('disclosure', $list['disclosure']);
                $statement->bindParam('registeredDepartment', $list['registeredDepartment']);
                $statement->bindParam('caseNumber', $list['caseNumber']);
                $statement->bindValue('searchInitiator', (string) $list['searchInitiator']);

                $accidentMemo  = (string) $list['accidentMemo'];
                $takenMeasures = (string) $list['takenMeasures'];

                $statement->bindParam('accidentMemo', $accidentMemo, length: strlen($accidentMemo));
                $statement->bindParam('takenMeasures', $takenMeasures, length: strlen($takenMeasures));

                try {
                    $statement->executeStatement();
                } catch (\Throwable) {
                    throw new \Exception('Ошибка вставки данных в TEK_SUMMARY_LIST');
                }
            }
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
