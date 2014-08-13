<?php

namespace JP\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 * @Template()
 */
class AdminController extends Controller {

	/**
	 * @Route("/users/all", name="user_list")
	 * @Template()
	 */
	public function allUsersAction() {

		$userManager = $this->container->get('fos_user.user_manager');

		return array(
			'allUsers' => $userManager->findUsers(),
		);
	}

	/**
	 * @Route("/users/create", name="user_create")
	 * @Template()
	 */
	public function userCreateAction(Request $request) {

		$em = $this->getDoctrine()->getManager();

		$userManager = $this->container->get('fos_user.user_manager');
		$user = $userManager->createUser();
		$user->setEnabled(true);

		$form = $this->createForm('user', $user, array('includePassword' => true, 'roles' => $this->container->getParameter('security.role_hierarchy.roles')));

		$form->handleRequest($request);

		if ($form->isValid()) {
			$user = $form->getData();
			$userManager->updateUser($user);

			$this->get('session')->getFlashBag()->add('success', $user->getFullName() . ' has been saved');
			return $this->redirect($this->generateUrl('user_edit', array('id' => $user->getID())));
		}

		return array(
			'form' => $form->createView(),
		);
	}

	/**
	 * @Route("/users/edit/{id}", name="user_edit")
	 * @Template()
	 */
	public function editUserAction($id, Request $request) {
		$currentUser = $this->get('security.context')->getToken()->getUser();

		if ($currentUser->getID() == $id) { //users cannot edit themselves!
			throw $this->createAccessDeniedException('You cannot edit your own user');
		}

		$userManager = $this->container->get('fos_user.user_manager');
		$user = $userManager->findUserBy(array('id' => $id));

		$form = $this->createForm('user', $user, array('roles' => $this->container->getParameter('security.role_hierarchy.roles')));

		$form->handleRequest($request);

		if ($form->isValid()) {
			$userManager->updateUser($user);

//			if ($currentUser->getID() == $id) { //if updating self, then refresh the token
//				$token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken($currentUser, null, 'main', $currentUser->getRoles());
//				$this->container->get('security.context')->setToken($token);
//			}

			$this->get('session')->getFlashBag()->add('success', $user->getFullName() . ' has been updated');

			return $this->redirect($this->generateUrl('user_edit', array('id' => $id)));
		}

		return array(
			'form' => $form->createView(),
			'user' => $user,
		);
	}
}
