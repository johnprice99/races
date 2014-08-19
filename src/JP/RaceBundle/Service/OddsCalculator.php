<?php

namespace JP\RaceBundle\Service;

use Doctrine\ORM\EntityManager;
use JP\RaceBundle\Entity\Race;

class OddsCalculator {

	private $em;

	public function __construct(EntityManager $entityManager) {
		$this->em = $entityManager;
	}

	public function calculate(Race $race) {
		$defaultProbability = (1 / $race->getRunnerCount()) * 100;
		foreach ($race->getEntries() as $entry) {
			//each horse needs to have it's probability based on the default, then altered from age etc
			$probability = $defaultProbability;

			//adjust probability based on age
			switch($entry->getHorse()->getAge()) {
				case 7:
				case 8:
					$probability += 8; //best age
					break;
				case 6:
				case 9:
					$probability += 5; // maturing
					break;
				default:
					$probability -= 5; // too young or too old
					break;
			}

			//adjust based on horse preferred type (flat/jump)
			$raceType = $race->getType();
			if ($entry->getHorse()->getPreferredType() === $raceType) {
				$probability += 10;
			}

			//adjust based on jockey weight
			switch ($raceType) {
				case 'flat':
					//8 to <10
					if ($entry->getJockey()->getNumericWeight() >= 8 && $entry->getJockey()->getNumericWeight() < 10) {
						$probability += 10;
					}
					else {
						$probability -= 8;
					}
					break;
				case 'jump':
					//9 <11
					if ($entry->getJockey()->getNumericWeight() >= 9 && $entry->getJockey()->getNumericWeight() < 11) {
						$probability += 10;
					}
					else {
						$probability -= 8;
					}
					break;
			}

			//adjust probability based on jockey skill level
			if ($entry->getJockey()->getLevel() < 60) {
				$probability -= 10;
			}
			elseif ($entry->getJockey()->getLevel() >= 90) {
				$probability += 8;
			}
			elseif ($entry->getJockey()->getLevel() >= 80) {
				$probability += 3;
			}

			//adjust probability based on trainer skill level
			if ($entry->getHorse()->getTrainer()->getLevel() < 60) {
				$probability -= 10;
			}
			elseif ($entry->getHorse()->getTrainer()->getLevel() >= 90) {
				$probability += 8;
			}
			elseif ($entry->getHorse()->getTrainer()->getLevel() >= 80) {
				$probability += 3;
			}

			//TODO
			//adjust probability based on form / last race

			//sanity check
			if ($probability <= 0) {
				$probability = 2;
			}

			$odds = (100 - $probability) / $probability;
			if ($probability > 50) {
				$odds = 1 / $odds;
			}
			$odds = number_format($odds);
			$entry->setOdds($odds.'-1');
//			$odds = number_format($probability);
//			$entry->setOdds($odds);
		}

		//save the race changes
		$this->em->persist($race);
		$this->em->flush();

		return true;
	}

}