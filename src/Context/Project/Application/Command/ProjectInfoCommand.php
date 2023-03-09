<?php

declare(strict_types=1);

namespace App\Context\Project\Application\Command;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @see ProjectInfoCommandHandler
 */
#[AsCommand(name: 'project:git-repo-info')]
class ProjectInfoCommand extends Command
{

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $response = $this->httpClient->request(
            'GET',
            'https://api.github.com/repos/simtel/symfony-project'
        );

        $io->info('Status Code:' . $response->getStatusCode());

        $it = new RecursiveArrayIterator($response->toArray());
        $iterator = new RecursiveIteratorIterator($it);
        $rows = [];
        foreach ($iterator as $key => $value) {
            $rows[] = [$key, $value];
        }

        $io->table(['field', 'value'], $rows);
        return Command::SUCCESS;
    }
}
