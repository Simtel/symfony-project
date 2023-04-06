<?php

declare(strict_types=1);

namespace App\Context\User\Application\Command;

use App\Context\Common\Infrastructure\Service\DoctrineConsoleLogger;
use App\Context\User\Domain\Contract\UserRepositoryInterface;
use App\Context\User\Domain\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'relations:example:users')]
class DoctrineRelationExampleUser extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepositoryInterface $userRepository,
        private readonly LoggerInterface $commandLogger,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(
            new DoctrineConsoleLogger($output, true)
        );

        $io = new SymfonyStyle($input, $output);

        $this->commandLogger->info('Start ' . $this->getName() . ' command at' . date('Y-m-d H:i:s'));

        $this->findAll($io);
        $this->findAllWithLocations($io);

        $this->commandLogger->info('End  ' . $this->getName() . ' command at' . date('Y-m-d H:i:s'));

        return Command::SUCCESS;
    }

    /**
     * Separate queries for get locations
     * @param SymfonyStyle $io
     */
    private function findAll(SymfonyStyle $io): void
    {
        $users = $this->userRepository->findAll();

        $headers = ['id', 'name', 'email', 'locations'];
        $rows = [];
        foreach ($users as $user) {
            $rows[] = [
                $user->getId(),
                $user->getName(),
                $user->getEmail(),
                implode(
                    ',',
                    array_map(static fn (Location $location): string => $location->getName(), $user->getLocations())
                )
            ];
        }
        $io->table($headers, $rows);
    }

    /**
     * Separate queries for get locations
     * @param SymfonyStyle $io
     */
    private function findAllWithLocations(SymfonyStyle $io): void
    {
        /**
         * SELECT u0_.id                 AS id_0,
         * u0_.email              AS email_1,
         * u0_.password           AS password_2,
         * u0_.name               AS name_3,
         * u0_.token              AS token_4,
         * u0_.secret_key         AS secret_key_5,
         * u0_.blocked_start_date AS blocked_start_date_6,
         * u0_.blocked_end_date   AS blocked_end_date_7,
         * l1_.id                 AS id_8,
         * l1_.name               AS name_9
         * FROM user u0_
         * LEFT JOIN user_location u2_ ON u0_.id = u2_.user_id
         * LEFT JOIN location l1_ ON l1_.id = u2_.location_id
         * ORDER BY l1_.name ASC
         **/

        $users = $this->userRepository->findAllWithLocations();

        $headers = ['id', 'name', 'email', 'locations'];
        $rows = [];
        foreach ($users as $user) {
            $rows[] = [
                $user->getId(),
                $user->getName(),
                $user->getEmail(),
                implode(
                    ',',
                    array_map(static fn (Location $location): string => $location->getName(), $user->getLocations())
                )
            ];
        }
        $io->table($headers, $rows);
    }
}
