<?php

declare(strict_types=1);

namespace App\Context\Common\Infrastructure\ParamConverter;

use App\Context\Common\Application\Dto\CreateConfigDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class CreateConfigDtoValueResolver implements
    ValueResolverInterface
{
    public function __construct(
        private ValidatorInterface $validator
    ) {
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

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return CreateConfigDto[]
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== CreateConfigDto::class) {
            return [];
        }

        /**
         * @var array{name:string,value:string} $payload
         */
        $payload = $request->toArray();

        $errors = $this->validator->validate($payload, $this->prepareConstraintCollection());

        if (count($errors) > 0) {
            throw new ValidationFailedException(null, $errors);
        }

        $dto = new CreateConfigDto($payload['name'], $payload['value']);

        return[$dto];
    }
}
