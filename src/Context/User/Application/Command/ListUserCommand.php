<?php

declare(strict_types=1);

namespace App\Context\User\Application\Command;

use App\Context\User\Domain\Contract\UserRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'users:list')]
class ListUserCommand extends Command
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $users = $this->userRepository->findAll();

        $headers = ['id', 'name', 'email'];
        $rows = [];
        foreach ($users as $user) {
            $rows[] = [$user->getId(), $user->getName(), $user->getEmail()];
        }
        $io->table($headers, $rows);

        return Command::SUCCESS;
    }
}
