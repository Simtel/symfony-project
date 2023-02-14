<?php

declare(strict_types=1);

namespace App\Context\User\Application\Command;

use App\Context\User\Domain\Contract\UserRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @see ListUserCommandHandler
 */
#[AsCommand(name: 'users:export')]
class ExportUserCommand extends Command
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly LoggerInterface $commandLogger,
        private readonly Filesystem $filesystem,
        private readonly string $publicDir,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->commandLogger->info('Start ' . $this->getName() . ' command at' . date('Y-m-d H:i:s'));

        $users = $this->userRepository->findAll();

        $headers = ['id', 'name', 'email'];
        $rows = [];
        foreach ($users as $user) {
            $rows[] = [$user->getId(), $user->getName(), $user->getEmail()];
        }
        $io->table($headers, $rows);

        $fileName = $this->getFile();
        $this->exportToFile($fileName, $rows);

        $this->commandLogger->info('Export ' . count($rows) . ' users to file ' . $fileName);

        $this->commandLogger->info('End  ' . $this->getName() . ' command at' . date('Y-m-d H:i:s'));

        return Command::SUCCESS;
    }

    private function exportToFile(string $fileName, array $data): void
    {
        $uploadDir = $this->publicDir . '/upload';
        if (!$this->filesystem->exists($uploadDir)) {
            $this->filesystem->mkdir($uploadDir);
        }

        foreach ($data as $row) {
            $this->filesystem->appendToFile($fileName, implode(';', $row) . PHP_EOL);
        }
    }

    private function getFile(): string
    {
        $uploadDir = $this->publicDir . '/upload';
        if (!$this->filesystem->exists($uploadDir)) {
            $this->filesystem->mkdir($uploadDir);
        }
        $fileName = $uploadDir . '/users-' . date('Y-m-d') . '.csv';
        if ($this->filesystem->exists($fileName)) {
            $this->filesystem->remove($fileName);
        }
        return $fileName;
    }
}
