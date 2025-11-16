<?php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $firewall = $event->getRequest()->attributes->get('_firewall_context', 'none');
        $path = $request->getPathInfo();
        $authHeader = $request->headers->get('Authorization');

        error_log("PATH: $path | FIREWALL: $firewall | AUTH HEADER: $authHeader");
    }
}
