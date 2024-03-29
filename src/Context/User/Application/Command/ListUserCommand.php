<?php

declare(strict_types=1);

namespace App\Context\User\Application\Command;

use App\Context\User\Domain\Contract\UserRepositoryInterface;
use App\Context\User\Domain\Entity\Location;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @see ListUserCommandHandler
 */
#[AsCommand(name: 'users:list')]
class ListUserCommand extends Command
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly LoggerInterface $commandLogger,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->commandLogger->info('Start ' . $this->getName() . ' command at' . date('Y-m-d H:i:s'));

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

        $this->commandLogger->info('End  ' . $this->getName() . ' command at' . date('Y-m-d H:i:s'));

        return Command::SUCCESS;
    }
}
