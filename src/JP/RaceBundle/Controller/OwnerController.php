<?php

namespace JP\RaceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use JP\RaceBundle\Form\Type\OwnerType;

/**
 * @Route("/owners")
 */
class OwnerController extends Controller {

	/**
	 * @Route("/", name="owner_list")
	 * @Template()
	 */
	public function listAction() {
		$owners = $this->getDoctrine()->getRepository('JPRaceBundle:Owner')->findAll();

		return array(
			'owners' => $owners,
		);
	}

	/**
	 * @Route("/{id}", name="owner_view")
	 * @Template()
	 */
	public function viewAction($id) {
		$owner = $this->getDoctrine()->getRepository('JPRaceBundle:Owner')->find($id);

		if (!$owner) {
			throw $this->createNotFoundException('Owner not found');
		}

		return array(
			'owner' => $owner,
		);
	}

	/**
	 * @Route("/{id}/edit", name="owner_edit")
	 * @Template()
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function editAction($id, Request $request) {
		$em = $this->getDoctrine()->getManager();

		$owner = $em->getRepository('JPRaceBundle:Owner')->find($id);

		if (!$owner) {
			throw $this->createNotFoundException('Owner not found');
		}

		$form = $this->createForm(new OwnerType(), $owner);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$owner = $form->getData();
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', $owner->getName() . ' has been updated');
			return $this->redirect($this->generateUrl('owner_edit', array('id' => $id)));
		}

		return array(
			'owner' => $owner,
			'form' => $form->createView(),
		);
	}
}
