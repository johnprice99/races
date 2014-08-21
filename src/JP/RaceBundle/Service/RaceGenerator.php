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
		//class 1 - levels 7, 8, 9 class 2 - level 4, 5, 6, 7, class 5 - level 1, 2, 3
		switch($race->getClass()) {
			case 1:
				$levels = array(8,9,10);
				break;
			case 2:
				$levels = array(4,5,6,7);
				break;
			case 3:
				$levels = array(1,2,3);
				break;
		}

		$raceType = $race->getType();
		$extraSQL = '';
		if ($raceType == 'flat' && $race->getMaiden()) {
			$extraSQL = ' AND flatMaiden = 1 ';
		}
		elseif ($raceType == 'jump' && $race->getMaiden()) {
			$extraSQL = ' AND jumpMaiden = 1 ';
		}

		//get the IDs of horses that match the criteria of the race
		$sql = 'SELECT x.* FROM (
			(
				SELECT id FROM `horse`
				WHERE `available` = 1
				AND `age` >= :age
				AND preferredType = :type
				AND level IN ('.implode(',', $levels).')'.$extraSQL.'
				ORDER BY RAND() LIMIT '.$race->getRunnerCount().'
			)
			UNION
			(
				SELECT id FROM `horse`
				WHERE `available` = 1
				AND `age` >= :age
				AND level IN ('.implode(',', $levels).')'.$extraSQL.'
				ORDER BY RAND() LIMIT '.$race->getRunnerCount().')
				LIMIT '.$race->getRunnerCount().'
			) x ORDER BY RAND()';
		$result = $this->em->getConnection()->fetchAll($sql, array(
			'age' => $race->getMinAge(),
			'type' => $raceType
		));
		$horsesInRace = array();
		foreach ($result as $id) { $horsesInRace[] = $id['id']; }

		if (empty($horsesInRace) || count($horsesInRace) < $race->getRunnerCount()) {
			die('not enough horses to fill the race');
		}

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