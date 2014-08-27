<?php

namespace JP\RaceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use JP\RaceBundle\Form\Type\HorseType;

/**
 * @Route("/horses")
 */
class HorseController extends Controller {

	/**
	 * @Route("/", name="horse_list")
	 * @Template()
	 */
	public function listAction() {
		$horses = $this->getDoctrine()->getRepository('JPRaceBundle:Horse')->findAll();

		return array(
			'horses' => $horses,
		);
	}

	/**
	 * @Route("/{id}", name="horse_view")
	 * @Template()
	 */
	public function viewAction($id) {
		$horse = $this->getDoctrine()->getRepository('JPRaceBundle:Horse')->find($id);

		return array(
			'horse' => $horse,
		);
	}

	/**
	 * @Route("/{id}/edit", name="horse_edit")
	 * @Template()
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function editAction($id, Request $request) {
		$em = $this->getDoctrine()->getManager();

		$horse = $em->getRepository('JPRaceBundle:Horse')->find($id);

		if (!$horse) {
			throw $this->createNotFoundException('Horse not found');
		}

		$form = $this->createForm(new HorseType(), $horse);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$horse = $form->getData();
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', $horse->getName() . ' has been updated');
			return $this->redirect($this->generateUrl('horse_edit', array('id' => $id)));
		}

		return array(
			'horse' => $horse,
			'form' => $form->createView(),
		);
	}
}
