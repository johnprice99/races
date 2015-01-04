<?php

namespace JP\RaceBundle\Service;

use Doctrine\ORM\EntityManager;
use JP\RaceBundle\Entity\Race;

class OddsCalculator {

	private $em;

	private $parameters;

	public function __construct(EntityManager $entityManager, $parameters = array()) {
		$this->em = $entityManager;
		$this->parameters = $parameters;
	}

	public function greatestCommonDivisor($a, $b) {
		$a = abs($a);
		$b = abs($b);
		if ($a < $b) {
			list($b, $a) = array($a, $b);
		}
		if ($b == 0) {
			return $a;
		}
		$r = $a % $b;
		while ($r > 0) {
			$a = $b;
			$b = $r;
			$r = $a % $b;
		}

		return $b;
	}

	public function roundToAny($n, $x, $closest = true) {
		if ($closest) {
			return (round($n) % $x === 0) ? round($n) : round(($n + $x / 2) / $x) * $x;
		}
		else {
			return round(($n+$x/2)/$x)*$x;
		}
	}

	public function calculate(Race $race) {
		$defaultScore = number_format((1 / $race->getRunnerCount()) * 100);
		$totalScore = 0; //used to work out percentages
		$topScore = 0;

		$raceType = $race->getType();
		$raceDistance = $race->getDistance();

		$configName = ($raceType === 'flat') ? 'flat' : 'jump';

		foreach ($race->getEntries() as $entry) {
			//each horse needs to have it's probability based on the default, then altered from age etc
			$entryScore = $defaultScore;
			$horse = $entry->getHorse();

			//adjust $entryScore based on age
			if (in_array($horse->getAge(), $this->parameters['horse']['best_age'])) {
				$entryScore += 10; //best age
			}
			elseif (in_array($horse->getAge(), $this->parameters['horse']['ok_age'])) {
				$entryScore += 5;  // maturing / declining
			}
			else {
				$entryScore -= 5; // too young or too old
			}

			//adjust based on horse preferred type (flat/jump) - removing as you wont get a flat horse on a jump race etc
//			if ($horse->getPreferredType() === $raceType) {
//				$entryScore += 10;
//			}

			if ($raceDistance >= (0.75 * $this->parameters['race'][$configName]['max_distance'])) {
				//if >75% of top distance for type of race - horses with 85+ stamina get boost
				if ($horse->getStamina() >= 85) {
					$entryScore += 5;
				}
				else {
					$entryScore -= 5;
				}
			}
			elseif($raceDistance >= (0.5 * $this->parameters['race'][$configName]['max_distance'])) {
				//if >50% of top distance for type of race - horses with 65+ stamina get boost
				if ($horse->getStamina() >= 65) {
					$entryScore += 5;
				}
				else {
					$entryScore -= 5;
				}
			}

			if ($entry->getJockey()->getNumericWeight() >= $this->parameters['race'][$configName]['ideal_weight']['min'] && $entry->getJockey()->getNumericWeight() < $this->parameters['race'][$configName]['ideal_weight']['max']) {
				$entryScore += 10;
			}
			else {
				$entryScore -= 10;
			}

			//adjust $entryScore based on jockey skill level
			if ($entry->getJockey()->getLevel() < 60) {
				$entryScore -= 10;
			}
			elseif ($entry->getJockey()->getLevel() >= 90) {
				$entryScore += 10;
			}
			elseif ($entry->getJockey()->getLevel() >= 80) {
				$entryScore += 3;
			}

			//adjust $entryScore based on trainer skill level
			if ($horse->getTrainer()->getLevel() < 60) {
				$entryScore -= 10;
			}
			elseif ($horse->getTrainer()->getLevel() >= 90) {
				$entryScore += 10;
			}
			elseif ($horse->getTrainer()->getLevel() >= 80) {
				$entryScore += 3;
			}

			//adjust $entryScore based on form / last 6 races
			$form = $horse->getForm();
			for ($i = 0; $i < 6; $i++) {
				if (isset($form[$i]) && is_numeric($form[$i]) && $form[$i] != 0) {
					$entryScore += 10 - $form[$i];
				}
			}

			//sanity check
			if ($entryScore <= 0) {
				$entryScore = 2;
			}

			$totalScore += $entryScore;
			$entry->setScore($entryScore);

			//used to work out the favourite
			if ($entryScore > $topScore) {
				$topScore = $entryScore;
			}
		}

		$favouritePicked = false;

		//now work out the percentage for each entry
		foreach ($race->getEntries() as $entry) {
			if (!$favouritePicked && $entry->getScore() === $topScore) {
				$entry->setFavourite(true);
				$favouritePicked = true;
			}

			$percentage = number_format(($entry->getScore() / $totalScore) * 100);

			//finally work out the odds based on that percentage
			$odds = array($entry->getScore() / (100 - $percentage), 1);
			if ($percentage <= 50) {
				$odds[0] = 1 / $odds[0];
			}
			if (!ctype_digit("" . $odds[0])) {
				$odds[0] = (100 - $percentage);
				$odds[1] = $percentage;
			}

			//round to closest 5
			$odds[0] = $this->roundToAny($odds[0], 5);
			$odds[1] = $this->roundToAny($odds[1], 5, false); //this must round up, otherwise we could end up with a 0!

			$gcd = $this->greatestCommonDivisor($odds[0], $odds[1]);
			$odds = array($odds[0] / $gcd, $odds[1] / $gcd);

			//sanity check - dont offer evens!
			if ($odds[0] == 1 && $odds[1] == 1) { $odds[0] = 2; }

			$entry->setOdds(implode('/', $odds));

//			echo $entry->getHorse()->getName() . ' - Score: ' . $entry->getScore() . '(' . $percentage . '%) - ' . $odds . '<br />';
		}

		//save the race changes
		$this->em->persist($race);
		$this->em->flush();

		return true;
	}

}