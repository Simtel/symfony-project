<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\ParamConverter;

use App\Context\Common\Application\Dto\CreateConfigDto;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class CreateConfigDtoParamConverter implements
    ParamConverterInterface
{
    public function __construct(
        private ValidatorInterface $validator
    ) {
    }

    /**
     * @inheritDoc
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $payload = $request->toArray();

        $errors = $this->validator->validate($payload, $this->prepareConstraintCollection());

        if (count($errors) > 0) {
            throw new ValidationFailedException(null, $errors);
        }

        $dto = new CreateConfigDto($payload['name'], (string)$payload['value']);

        $request->attributes->set($configuration->getName(), $dto);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function supports(ParamConverter $configuration): bool
    {
        return $configuration->getClass() === CreateConfigDto::class;
    }

    private function prepareConstraintCollection(): Collection
    {
        return new Collection(
            [
                'name' => [new Required(), new Type('string'), new NotBlank()],
                'value' => [new Required(), new Type('string')]
            ]
        );
    }
}
