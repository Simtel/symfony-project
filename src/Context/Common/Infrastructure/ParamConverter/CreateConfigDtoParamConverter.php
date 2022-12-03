<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\ParamConverter;

use App\Context\Common\Application\Dto\CreateConfigDto;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class CreateConfigDtoParamConverter implements
    ParamConverterInterface
{
    /**
     * @inheritDoc
     */
    public function apply(Request $request, ParamConverter $configuration): void
    {
        $payload = $request->toArray();

        $dto = new CreateConfigDto($payload['name'], (string)$payload['value']);

        $request->attributes->set($configuration->getName(), $dto);
    }

    /**
     * @inheritDoc
     */
    public function supports(ParamConverter $configuration): bool
    {
        return $configuration->getClass() === CreateConfigDto::class;
    }
}
