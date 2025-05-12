<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\Service;


use Symfony\Component\Console\Output\OutputInterface;

class DoctrineConsoleLogger
{
    private OutputInterface $output;
    private bool $showParams;
    private bool $showTypes;

    public function __construct(OutputInterface $output, bool $showParams = false, bool $showTypes = false)
    {
        $this->output = $output;
        $this->showParams = $showParams;
        $this->showTypes = $showTypes;
    }

    public function startQuery($sql, ?array $params = null, ?array $types = null): void
    {
        $this->output->writeln('Start query =======');
        $this->output->writeln($sql);

        if ($this->showParams && $params) {
            $this->output->writeln('- params:');
            $this->output->writeln(print_r($params, true));
        }

        if ($this->showTypes && $types) {
            $this->output->writeln('- types:');
            $this->output->writeln(print_r($types, true));
        }
    }

    public function stopQuery(): void
    {
        $this->output->writeln('Stop query =======');
    }
}
