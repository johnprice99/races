<?php

namespace JP\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\ProfileController as BaseController;

class ProfileController extends BaseController {

    public function showAction() { //do not want to allow this route, so just forward to the edit page
		$router = $this->container->get('router');
		return new RedirectResponse($router->generate('fos_user_profile_edit'), 302);
    }

	public function editAction(Request $request) {
		$user = $this->container->get('security.context')->getToken()->getUser();

		if (!$user->isAccountNonLocked()) { //if the user is locked, log them out as they shouldn't be able to access their account
			$router = $this->container->get('router');
			return new RedirectResponse($router->generate('fos_user_security_logout'));
		}
		return parent::editAction($request);
    }
}
