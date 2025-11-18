<?php
namespace App\EventSubscriber;

use App\Service\EncryptionService;
use Psr\Log\LoggerInterface;
use SodiumException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class RequestSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
        ];

        
    }

    public function __construct(private EncryptionService $encryptionService, private LoggerInterface $logger)
    {

    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if (strpos($request->getPathInfo(), '/api/templates') === 0) {
            $xapikey = $request->headers->get('x-api-key');
            $authorization = $request->headers->get('authorization');
            if(is_null($authorization)){
                $request->headers->set('authorization', $xapikey );
            }
            $this->logger->info('Request Headers new header authorization : ',  [$request->headers->get('authorization')]);
        }

                // Exclure la route /auth
        if (strpos($request->getPathInfo(), '/auth') === 0) {
            return;
        }

        if (strpos($request->getPathInfo(), '/api/templates') !== 0) {

            $data = json_decode($request->getContent(), true);

            if (!isset($data['email']) || !isset($data['password']) || !isset($data['nonceEmail']) || !isset($data['noncePassword'])) {
                return;
            }

            try {

                $key = base64_decode($data['key']);

                $email = $this->encryptionService->decrypt($data['email'], $data['nonceEmail'], $key);
                $password = $this->encryptionService->decrypt($data['password'], $data['noncePassword'], $key);

                if ($email === false || $password === false) {
                    throw new \RuntimeException('Décryptage échoué');
                }
                // Remplace les données chiffrées par les données déchiffrées
                $request->request->set('email', $email);
                $request->request->set('password', $password);

            } catch (SodiumException $e) {
                $this->logger->log('', " $email ko surement");
                // Log error, ne pas exposer l'erreur à l'utilisateur
            }
        }

    }

}
