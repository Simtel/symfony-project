<?php

namespace App\Context\User\Infrastructure\EventListener;

use JsonException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

#[AsEventListener]
class ResponseEventListener
{
    /**
     * @throws JsonException
     */
    public function __invoke(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        if ($request->attributes->get('_route') !== 'show_user_by_name') {
            return;
        }

        $response = $event->getResponse();

        $contentJson = $response->getContent();

        $content = json_decode((string)$contentJson, true, 512, JSON_THROW_ON_ERROR);

        if (is_array($content) && array_key_exists('name', $content)) {
            $content['replace'] = true;

            $response->setContent((string)json_encode($content, JSON_THROW_ON_ERROR));
        }
    }
}
