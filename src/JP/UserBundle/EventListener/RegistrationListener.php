<?php

namespace JP\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class RegistrationListener implements EventSubscriberInterface {

    private $router;

    public function __construct(UrlGeneratorInterface $router) {
        $this->router = $router;
    }

    public static function getSubscribedEvents() {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
			FOSUserEvents::RESETTING_RESET_SUCCESS => 'redirectToProfile',
			FOSUserEvents::PROFILE_EDIT_SUCCESS => 'redirectToProfile',
			FOSUserEvents::CHANGE_PASSWORD_SUCCESS => 'redirectToProfile',
        );
    }

//    public function onRegistrationComplete(FilterUserResponseEvent $event) {
//        $profile = new Profile($event->getUser());
//    }

    public function onRegistrationSuccess(FormEvent $event) {
        $user = $event->getForm()->getData();
        $user->setRoles(array('ROLE_USER'));

//        $user = $event->getForm()->getData();
//
//        $form = $event->getForm();
//        $profile = new Profile($user);
//        $profile->setFirstName($form['firstName']->getData());
//        $profile->setLastName($form['lastName']->getData());
//        $user->setProfile($profile);
//
//        $url = $this->router->generate('user_profile');
//        $event->setResponse(new RedirectResponse($url));
    }

    public function redirectToProfile(FormEvent $event) {
        $url = $this->router->generate('fos_user_profile_edit');
        $event->setResponse(new RedirectResponse($url));
    }
}