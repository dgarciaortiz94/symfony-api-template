<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationSuccessListener implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            'lexik_jwt_authentication.on_authentication_success' => 'onAuthenticationSuccess',
        ];
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $data['user'] = [
            'username' => $user->getUsername(),
            'name' => $user->getName(),
            'firstSurname' => $user->getFirstSurname(),
            'secondSurname' => $user->getLastSurname(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ];

        $event->setData($data);
    }
}