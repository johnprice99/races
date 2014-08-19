<?php

namespace JP\RaceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JP\RaceBundle\Entity\Race;
use JP\RaceBundle\Entity\Entry;

class RaceController extends Controller {

	/**
	 * @Route("/race/generate")
	 * @Template()
	 */
	public function generateAction() {

		$em = $this->getDoctrine()->getManager();

		//create a race
		$race = new Race();

		//pick random horses
		$result = $em->getConnection()->fetchAll('SELECT id FROM `horse` WHERE `available` = 1 ORDER BY rand() LIMIT ' . $race->getRunnerCount());
		$horsesInRace = array();
		foreach ($result as $id) { $horsesInRace[] = $id['id']; }

		$repository = $this->getDoctrine()->getRepository('JPRaceBundle:Horse');
		$query = $repository->createQueryBuilder('h')
			->where('h.id IN (:ids)')
			->setParameter('ids', $horsesInRace)
			->getQuery();

		$runners = $query->getResult();
		shuffle($runners);

		foreach ($runners as $runner) {
			//set the horse as no longer available
			$runner->setAvailable(false);

			//create the entry
			$entry = new Entry();
			$entry->setHorse($runner);

			//pick a jockey
			$jockeyRepo = $this->getDoctrine()->getRepository('JPRaceBundle:Jockey');
			$count = $jockeyRepo->createQueryBuilder('j')->select('COUNT(j)')->where('j.available = 1')->getQuery()->getSingleScalarResult();
			$jockey = $jockeyRepo->createQueryBuilder('j')->where('j.available = 1')
				->setFirstResult(rand(0, $count - 1))
				->setMaxResults(1)
				->getQuery()->getSingleResult();
			$entry->setJockey($jockey);

			//set the jockey as no longer available
			$jockey->setAvailable(false);
			$em->persist($jockey);
			$em->flush();

			//add the entry to the race
			$race->addEntry($entry);
		}

		//calculate the odds
		$this->get('jp.odds.calculator')->calculate($race);

		//save the race
		$em->persist($race);
		$em->flush();

		die('race created: ' . $race->getId());

		return array();
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
	 * @Route("/race/winner/{id}", name="race_winner")
	 * @Template()
	 */
	public function winnerAction($id) {
		//get the race
		$repository = $this->getDoctrine()->getRepository('JPRaceBundle:Race');
		$race = $repository->createQueryBuilder('r')->where('r.id = :id')->setParameter('id', $id)->setMaxResults(1)->getQuery()->getSingleResult();

		$this->get('jp.race.engine')->run($race);

		echo 'winner done';
		die();

		//pass the race to the view to render the race card
		return array(
			'race' => $race
		);
	}
}
