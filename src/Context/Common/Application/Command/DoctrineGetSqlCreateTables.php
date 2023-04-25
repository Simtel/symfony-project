<?php

declare(strict_types=1);

namespace App\Context\Common\Application\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('example:doctrine-get-sql-create-tables')]
class DoctrineGetSqlCreateTables extends Command
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

        foreach ($metadataFactory->getAllMetadata() as $metaData) {
            $schemaTool = new SchemaTool($this->entityManager);
            $io->info($metaData->getName());
            $io->writeln($schemaTool->getCreateSchemaSql([$metaData]));
        }

        return Command::SUCCESS;
    }
}
