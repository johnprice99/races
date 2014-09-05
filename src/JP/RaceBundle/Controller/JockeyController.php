<?php

namespace JP\RaceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use JP\RaceBundle\Form\Type\JockeyType;

/**
 * @Route("/jockeys")
 */
class JockeyController extends Controller {

	/**
	 * @Route("/", name="jockey_list")
	 * @Template()
	 */
	public function listAction() {
		$jockeys = $this->getDoctrine()->getRepository('JPRaceBundle:Jockey')->findAll();

		return array(
			'jockeys' => $jockeys,
		);
	}

	/**
	 * @Route("/{id}", name="jockey_view")
	 * @Template()
	 */
	public function viewAction($id) {
		$jockey = $this->getDoctrine()->getRepository('JPRaceBundle:Jockey')->find($id);

		if (!$jockey) {
			throw $this->createNotFoundException('Jockey not found');
		}

		return array(
			'jockey' => $jockey,
		);
	}

	/**
	 * @Route("/{id}/edit", name="jockey_edit")
	 * @Template()
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function editAction($id, Request $request) {
		$em = $this->getDoctrine()->getManager();

		$jockey = $em->getRepository('JPRaceBundle:Jockey')->find($id);

		if (!$jockey) {
			throw $this->createNotFoundException('Jockey not found');
		}

		$form = $this->createForm(new JockeyType(), $jockey);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$jockey = $form->getData();
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', $jockey->getName() . ' has been updated');
			return $this->redirect($this->generateUrl('jockey_edit', array('id' => $id)));
		}

		return array(
			'jockey' => $jockey,
			'form' => $form->createView(),
		);
	}
}
