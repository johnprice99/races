<?php

namespace JP\RaceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/race")
 */
class RaceController extends Controller {

	/**
	 * @Route("/list", name="race_list")
	 * @Template()
	 */
	public function listAction() {
		//get the races
		$races = $this->getDoctrine()->getRepository('JPRaceBundle:Race')->findAll();

		return array(
			'races' => $races
		);
	}

	/**
	 * @Route("/view/{id}", name="race_view")
	 * @Template()
	 */
	public function viewAction($id) {
		//get the race
		$repository = $this->getDoctrine()->getRepository('JPRaceBundle:Race');
		$race = $repository->createQueryBuilder('r')->where('r.id = :id')->setParameter('id', $id)->setMaxResults(1)->getQuery()->getSingleResult();

		if ($race->getComplete()) {
			return $this->render('JPRaceBundle:Race:result.html.twig', array('race' => $race));
		}

		return $this->render('JPRaceBundle:Race:racecard.html.twig', array('race' => $race));
	}

	/**
	 * @Route("/run", name="race_run_all")
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function runAllAction() {
		$races = $this->getDoctrine()->getRepository('JPRaceBundle:Race')->findAll();
		foreach ($races as $race) {
			if (!$race->getComplete()) {
				$this->get('jp.race.engine')->run($race);
			}
		}

		return $this->redirect($this->generateUrl('race_list'));
	}

	/**
	 * @Route("/run/{id}", name="race_run")
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function runAction($id) {
		$race = $this->getDoctrine()->getRepository('JPRaceBundle:Race')->find($id);
		if (!$race->getComplete()) {
			$this->get('jp.race.engine')->run($race);
		}

		return $this->redirect($this->generateUrl('race_view', array('id' => $id)));
	}

	/**
	 * @Route("/clear", name="race_clear")
	 * @Security("has_role('ROLE_ADMIN')")
	 */
	public function clearAction() {
		$em = $this->getDoctrine()->getManager();

		//delete all races
		$em->createQuery('DELETE JPRaceBundle:Race r')->execute();

		//find all horses and set their availability to 1
		$em->createQuery('UPDATE JPRaceBundle:Horse h SET h.available = 1')->execute();

		//find all jockeys and set their availability to 1
		$em->createQuery('UPDATE JPRaceBundle:Jockey j SET j.available = 1')->execute();

		return $this->redirect($this->generateUrl('race_list'));
	}
}
