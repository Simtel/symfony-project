<?php

declare(strict_types=1);

namespace App\Context\Common\Application\Contract;

use App\Context\Common\Infrastructure\View\ConfigListView;
use App\Context\Common\Infrastructure\View\ConfigView;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Uid\Uuid;

interface ConfigProviderInterface
{
    public function getList(): ConfigListView;

    /**
     * @throws EntityNotFoundException
     */
    public function findById(Uuid $id): ConfigView;

    public function delete(Uuid $id): void;
}
