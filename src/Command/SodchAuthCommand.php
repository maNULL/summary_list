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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name       : 'app:sodch:auth',
    description: 'Authentification in SODCH service',
    hidden     : true
)]
class SodchAuthCommand extends Command
{
    public function __construct(
        private readonly ClientInterface       $sodchClient,
        private readonly ParameterBagInterface $parameterBag
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

        $content = $this
            ->sodchClient
            ->get('/mvd-server/sso')
            ->getBody()
            ->getContents();

        if (preg_match("/name=\'SAMLRequest\' value=\'(.*?)\' \/>/si", $content, $result)) {
            $this->sodchClient->request(
                'GET',
                'http://idp.sudis.mvd.ru/idp/profile/SAML2/POSTGOST/SSO',
                [
                    'query'   => ['SAMLRequest' => $result[1], 'RelayState' => '/mvd-server/sso'],
                    'headers' => ['Content-Type' => 'application/x-www-form-urlencoded;'],
                ]
            );
        } else {
            $io->error('Ошибка получение SAMLRequest');

            return Command::FAILURE;
        }

        $response = $this
            ->sodchClient
            ->post(
                'http://idp.sudis.mvd.ru/idp/api/login/password',
                [
                    'headers' => ['Accept: application/json', 'Content-Type: application/json'],
                    'json'    => [
                        'userLogin'    => $this->parameterBag->get('sodch_username'),
                        'userPassword' => $this->parameterBag->get('sodch_password'),
                    ],
                ]
            )
            ->getBody()
            ->getContents();

        if (strlen($response) > 0) {
            $json = json_decode($response, true);
        } else {
            $io->error('Ошибка авторизации в СУДИС');

            return Command::FAILURE;
        }

        if (isset($json['code']) && is_null($json['synopsis'])) {
            $content = $this
                ->sodchClient
                ->request('GET', 'http://idp.sudis.mvd.ru/idp/authentication')
                ->getBody()
                ->getContents();

            if (preg_match("/name=\"SAMLResponse\" value=\"(.*?)\">/si", $content, $result)) {
                $this->sodchClient->request(
                    'POST',
                    '/mvd-server/sso',
                    [
                        'form_params' => ['SAMLResponse' => $result[1], 'RelayState' => '/mvd-server/sso'],
                    ]
                );
            } else {
                $io->error('Ошибка получение SAMLResponse');

                return Command::FAILURE;
            }
        } else {
            $io->error('В результате авторизации в ответе "code" отсутствует или Установлено значение для "synopsys"');

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
