<?php

namespace JP\RaceBundle\Service;

use Doctrine\ORM\EntityManager;
use JP\RaceBundle\Service\OddsCalculator;
use JP\RaceBundle\Entity\Race;
use JP\RaceBundle\Entity\Entry;

class RaceGenerator {

	private $em;

	private $oddsCalculator;

	private $parameters;

	public function __construct(EntityManager $entityManager, OddsCalculator $oddsCalculator, $parameters = array()) {
		$this->em = $entityManager;
		$this->oddsCalculator = $oddsCalculator;
		$this->parameters = $parameters;
	}

	public function generate($options) {
		$runners = $options['runners'];
		$raceType = $options['type'];
		$raceClass = $options['class'];
//		$nursery = $options['nursery'];

		$configName = ($raceType === 'flat') ? 'flat' : 'jump';

		//create a race
		$race = new Race();
		$race->setRunnerCount($runners);
		$race->setType($raceType);
		$race->setClass($raceClass);
		$race->setMinAge(mt_rand(3, 5));
		$race->setMaiden(mt_rand(0, 1));
		$race->setDistance(mt_rand($this->parameters['race'][$configName]['min_distance'], $this->parameters['race'][$configName]['max_distance']));

		$extraSQL = '';
		if ($race->getMaiden()) {
			$extraSQL = ' AND '.$configName.'Maiden = 1 ';
		}

		$levels = $this->parameters['race']['class_levels'][$raceClass];

		//get the IDs of horses that match the criteria of the race
		$sql = 'SELECT id FROM `horse`
				WHERE `available` = 1
				AND `age` >= :age
				AND preferredType = :type
				AND level IN ('.implode(',', $levels).')'.$extraSQL.'
				GROUP BY `owner_id`
				ORDER BY RAND() LIMIT '.$race->getRunnerCount();

		$result = $this->em->getConnection()->fetchAll($sql, array(
			'age' => $race->getMinAge(),
			'type' => $raceType
		));
		$horsesInRace = array();
		foreach ($result as $id) { $horsesInRace[] = $id['id']; }

		if (empty($horsesInRace) || count($horsesInRace) < $race->getRunnerCount()) {
			die('not enough horses to fill the race (need '.$race->getRunnerCount().', found '.count($horsesInRace).')');
		}

		$repository = $this->em->getRepository('JPRaceBundle:Horse');
		$query = $repository->createQueryBuilder('h')
			->where('h.id IN (:ids)')
			->setParameter('ids', $horsesInRace)
			->getQuery();

		$runners = $query->getResult();
		shuffle($runners);

		foreach ($runners as $i => $runner) {
			//set the horse as no longer available
			$runner->setAvailable(false);

			//create the entry
			$entry = new Entry();
			$entry->setPositionDrawn($i+1);
			$entry->setHorse($runner);

			//pick a jockey that matches the type of race
			$jockeySQL = 'SELECT x.* FROM (
				(
					SELECT `id` FROM `jockey`
					WHERE `available` = 1
					AND `weight` >= :minWeight AND `weight` < :maxWeight
					ORDER BY RAND() LIMIT 1
				)
				UNION
				(
					SELECT `id` FROM `jockey`
					WHERE `available` = 1 ORDER BY RAND() LIMIT 1
				) LIMIT 1
			) x ORDER BY RAND()';
			$jockeyResult = $this->em->getConnection()->fetchAssoc($jockeySQL, array(
				'minWeight' => $this->parameters['race'][$configName]['ideal_weight']['min'],
				'maxWeight' => $this->parameters['race'][$configName]['ideal_weight']['max']
			));

			$repository = $this->em->getRepository('JPRaceBundle:Jockey');
			$query = $repository->createQueryBuilder('j')
				->where('j.id = :jockeyID')
				->setParameter('jockeyID', $jockeyResult['id'])
				->getQuery();

			$jockey = $query->getSingleResult();
			$entry->setJockey($jockey);
			$entry->setJockeyWeight($jockey->getNumericWeight());

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