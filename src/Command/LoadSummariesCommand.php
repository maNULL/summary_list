<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\DBAL\Connection;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name       : 'app:load-summaries',
    description: 'Получение и обработка данных из сервиса СОДЧ',
)]
class LoadSummariesCommand extends Command
{
    public function __construct(
        private readonly ClientInterface $sodchClient,
        private readonly Connection      $connection,
    )
    {
        parent::__construct();
    }

    protected function configure(): void {}

    /**
     * @throws GuzzleException
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

        if ($authCommand->run($input, $output) > 0) {
            $io->error('Ошибка аутентификации!');

            return Command::FAILURE;
        }

//        $io->note('Process start');
//
//        $processAllSummaryListCommand = $this->getApplication()->find('app:sodch:get-all-summaries');
//
//        if ($processAllSummaryListCommand->run($input, $output) > 0) {
//            $io->error('Ошибка загрузки summary list!');
//
//            return Command::FAILURE;
//        }

        $diffIds = $this->connection->executeQuery('select summary_id from summaries_diff order by summary_id');

        $io->note('Process start by ID');

        foreach ($diffIds->iterateColumn() as $id) {
            $getSummaryByIdCommand = $this->getApplication()->find('app:sodch:get-summary-by-id');

            $idInput = new ArrayInput(
                ['id' => $id]
            );

            if ($getSummaryByIdCommand->run($idInput, $output) > 0) {
                $io->error(sprintf('Ошибка обработки записи сводки с ID=%s!', $id));

                return Command::FAILURE;
            }
        }

        // Logout
        $this->sodchClient->get(
            'http://idp.sudis.mvd.ru/idp/Logout?logoutRedirectUrl=http://sodchm.it.mvd.ru/mvd'
        );

        return Command::SUCCESS;
    }
}
