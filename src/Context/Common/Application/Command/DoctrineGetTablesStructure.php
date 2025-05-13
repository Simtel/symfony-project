<?php

declare(strict_types=1);

namespace App\Context\Common\Application\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Utility\PersisterHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('example:doctrine-get-tables-structure')]
class DoctrineGetTablesStructure extends Command
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
        $headers = ['field', 'type'];
        foreach ($metadataFactory->getAllMetadata() as $metaData) {
            $io->info($metaData->getName());

            $rows = [];
            foreach ($metaData->getColumnNames() as $columnName) {
                $rows[] = [$columnName, PersisterHelper::getTypeOfColumn($columnName, $metaData, $this->entityManager)];
            }
            $io->table($headers, $rows);
        }

        return Command::SUCCESS;
    }
}
