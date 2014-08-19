<?php

namespace JP\RaceBundle\Service;

use Doctrine\ORM\EntityManager;
use JP\RaceBundle\Service\OddsCalculator;
use JP\RaceBundle\Entity\Race;
use JP\RaceBundle\Entity\Entry;

class RaceGenerator {

	private $em;

	private $oddsCalculator;

	public function __construct(EntityManager $entityManager, OddsCalculator $oddsCalculator) {
		$this->em = $entityManager;
		$this->oddsCalculator = $oddsCalculator;
	}

	public function generate() {
		//create a race
		$race = new Race();

		//pick random horses
		$result = $this->em->getConnection()->fetchAll('SELECT id FROM `horse` WHERE `available` = 1 ORDER BY rand() LIMIT ' . $race->getRunnerCount());
		$horsesInRace = array();
		foreach ($result as $id) { $horsesInRace[] = $id['id']; }

		$repository = $this->em->getRepository('JPRaceBundle:Horse');
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
			$jockeyRepo = $this->em->getRepository('JPRaceBundle:Jockey');
			$count = $jockeyRepo->createQueryBuilder('j')->select('COUNT(j)')->where('j.available = 1')->getQuery()->getSingleScalarResult();
			$jockey = $jockeyRepo->createQueryBuilder('j')->where('j.available = 1')
				->setFirstResult(mt_rand(0, $count - 1))
				->setMaxResults(1)
				->getQuery()->getSingleResult();
			$entry->setJockey($jockey);

			//set the jockey as no longer available
			$jockey->setAvailable(false);
			$this->em->persist($jockey);
			$this->em->flush();

			//add the entry to the race
			$race->addEntry($entry);
		}

		//calculate the odds
		$this->oddsCalculator->calculate($race);

		//save the race
		$this->em->persist($race);
		$this->em->flush();

		return true;
	}

}