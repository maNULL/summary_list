<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name       : 'app:get-summaries',
    description: 'Get all summary list from SODCH service',
)]
class GetSummariesCommand extends Command
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

    /**
     * @throws GuzzleException
     * @throws Exception
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        /*        $arg1 = $input->getArgument('arg1');

                if ($arg1) {
                    $io->note(sprintf('You passed an argument: %s', $arg1));
                }

                if ($input->getOption('option1')) {
                    // ...
                }

                $io->success('You have a new command! Now make it your own! Pass --help to see your options.');*/
        $io->note('Аутентификация...');

        $authCommand = $this->getApplication()->find('app:sodch:auth');
        $returnCode  = $authCommand->run($input, $output);

        if ($returnCode > 0) {
            $io->error('Ошибка аутентификации!');

            return Command::FAILURE;
        }

        $io->note('Process start');
        $this->processCurrentSummaryList();

        // Logout
        $this->sodchClient->get(
            'http://idp.sudis.mvd.ru/idp/Logout?logoutRedirectUrl=http://sodchm.it.mvd.ru/mvd'
        );

        return Command::SUCCESS;
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     * @throws \Exception
     */
    public function processCurrentSummaryList()
    {
        $summaryDate = \DateTime::createFromFormat('Y-m-d', $this->parameterBag->get('sodch_get_summary_list_from'));

        while ($summaryDate < (new \DateTimeImmutable('previous day'))) {
            $this->saveCurrentSummaryList(
                $this->getSummaryListByDate(new \DateTimeImmutable($summaryDate->format('c')))
            );

            $summaryDate->modify('+1 day');
        }
    }

    /**
     * @throws Exception
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
                    dd($list);
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

        $c = $this
            ->sodchClient
            ->request(
                'POST',
                '/mvd-server/summarynew/search',
                [
                    'form_params' => [
                        'summary' => json_encode($params),
                    ],
                ]
            )
            ->getBody()
            ->getContents();

        file_put_contents($summaryDate->format('Ymd').'.json', $c);

        return $c;
    }
}
