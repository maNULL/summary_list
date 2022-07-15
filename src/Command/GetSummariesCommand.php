<?php

declare(strict_types=1);

namespace App\Command;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name       : 'app:get-summaries',
    description: 'Get all summary list from SODCH service',
)]
class GetSummariesCommand extends Command
{
    public function __construct(
        private readonly ClientInterface $sodchClient,
    )
    {
        parent::__construct();
    }

    protected function configure(): void {}

    /**
     * @throws GuzzleException
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

        $io->note('Process start');

        $processAllSummaryListCommand = $this->getApplication()->find('app:sodch:get-all-summaries');

        if ($processAllSummaryListCommand->run($input, $output) > 0) {
            $io->error('Ошибка загрузки summary list!');

            return Command::FAILURE;
        }

        // Logout
        $this->sodchClient->get(
            'http://idp.sudis.mvd.ru/idp/Logout?logoutRedirectUrl=http://sodchm.it.mvd.ru/mvd'
        );

        return Command::SUCCESS;
    }
}
