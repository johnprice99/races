<?php

namespace JP\RaceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use JP\RaceBundle\Form\Type\TrainerType;

/**
 * @Route("/trainers")
 */
class TrainerController extends Controller {

	/**
	 * @Route("/", name="trainer_list")
	 * @Template()
	 */
	public function listAction() {
		$trainers = $this->getDoctrine()->getRepository('JPRaceBundle:Trainer')->findAll();

		return array(
			'trainers' => $trainers,
		);
	}

	/**
	 * @Route("/{id}", name="trainer_view")
	 * @Template()
	 */
	public function viewAction($id) {
		$trainer = $this->getDoctrine()->getRepository('JPRaceBundle:Trainer')->find($id);

		return array(
			'trainer' => $trainer,
		);
	}

	/**
	 * @Route("/{id}/edit", name="trainer_edit")
	 * @Template()
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function editAction($id, Request $request) {
		$em = $this->getDoctrine()->getManager();

		$trainer = $em->getRepository('JPRaceBundle:Trainer')->find($id);

		if (!$trainer) {
			throw $this->createNotFoundException('Trainer not found');
		}

		$form = $this->createForm(new TrainerType(), $trainer);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$trainer = $form->getData();
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', $trainer->getName() . ' has been updated');
			return $this->redirect($this->generateUrl('trainer_edit', array('id' => $id)));
		}

		return array(
			'trainer' => $trainer,
			'form' => $form->createView(),
		);
	}
}
