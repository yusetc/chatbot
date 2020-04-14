<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use function json_last_error;
use function json_last_error_msg;

class BeforeActionSubscriber implements EventSubscriberInterface
{
    /**
     * @param FilterControllerEvent $event
     */
    public function onConvertJsonStringToArray(FilterControllerEvent $event): void
    {
        $request = $event->getRequest();
        if ($request->getContentType() !== 'json' || !$request->getContent()) {
            return;
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('invalid json body: ' . json_last_error_msg());
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'convertJsonStringToArray' => 'onConvertJsonStringToArray',
        ];
    }
}
