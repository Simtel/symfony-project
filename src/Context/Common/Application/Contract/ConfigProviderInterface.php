<?php

declare(strict_types=1);

namespace App\Context\Common\Application\Contract;

use App\Context\Common\Infrastructure\View\ConfigListView;

interface ConfigProviderInterface
{
    public function getList(): ConfigListView;
}
