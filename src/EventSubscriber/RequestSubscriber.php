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
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        ];

        
    }

    public function __construct(private EncryptionService $encryptionService, private LoggerInterface $logger)
    {

    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if (strpos($request->getPathInfo(), '/api/templates') === 0) {
             $this->logger->info('Request Headers api/templates : ', $request->headers->all());
            $xapikey = $request->headers->get('x-api-key');
            $authorization = $request->headers->get('authorization');
            if(is_null($authorization)){
                $request->headers->set('Authorization', $xapikey );
            }
            return;
        }

                // Exclure la route /auth
        if (strpos($request->getPathInfo(), '/auth') === 0) {
            return;
        }


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


    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $message = 'pas de user';
        $user = $event->getAuthenticationToken()->getUser();
        if(!is_null($user)){
            $message = "email du user : " . $user->getEmail();
        }
        $this->logger->log('', " $message ");
        // Loggez des informations sur l'utilisateur
        // Utilisez le logger pour voir le nom d'utilisateur ou d'autres détails
    }








}
