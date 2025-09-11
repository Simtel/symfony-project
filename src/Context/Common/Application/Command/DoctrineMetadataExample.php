<?php

declare(strict_types=1);

namespace App\Context\Common\Application\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('example:doctrine-metadata')]
final class DoctrineMetadataExample extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $metadataFactory = $this->entityManager->getMetadataFactory();
        $headers = ['Class', 'Table', 'Columns'];
        $rows = [];
        foreach ($metadataFactory->getAllMetadata() as $metaData) {
            $rows[] = [
                $metaData->getName(),
                $metaData->getTableName(),
                implode(',' . PHP_EOL, $metaData->getColumnNames())
            ];
        }

        $io->table($headers, $rows);

        return Command::SUCCESS;
    }
}
