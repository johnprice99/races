<?php

namespace JP\RaceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class RaceController extends Controller {

	/**
	 * @Route("/race/generate", name="race_generate")
	 */
	public function generateAction() {
		$this->get('jp.race.generator')->generate();

		return $this->redirect($this->generateUrl('race_list'));
	}

	/**
	 * @Route("/race/clear", name="race_clear")
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

	/**
	 * @Route("/race/list", name="race_list")
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
	 * @Route("/race/view/{id}", name="race_view")
	 * @Template()
	 */
	public function viewAction($id) {
		//get the race
		$repository = $this->getDoctrine()->getRepository('JPRaceBundle:Race');
		$race = $repository->createQueryBuilder('r')->where('r.id = :id')->setParameter('id', $id)->setMaxResults(1)->getQuery()->getSingleResult();

		//pass the race to the view to render the race card
		return array(
			'race' => $race
		);
	}

	/**
	 * @Route("/race/run", name="race_run")
	 */
	public function runAction() {
		$races = $this->getDoctrine()->getRepository('JPRaceBundle:Race')->findAll();
		foreach ($races as $race) {
			$this->get('jp.race.engine')->run($race);
		}
		return $this->redirect($this->generateUrl('race_list'));
	}
}
