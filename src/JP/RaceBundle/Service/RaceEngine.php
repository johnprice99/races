<?php

namespace JP\RaceBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use JP\RaceBundle\Entity\Race;

class RaceEngine {

	private $em;

	public function __construct(EntityManager $entityManager) {
		$this->em = $entityManager;
	}

	public function run(Race $race) {
		$entries = $race->getEntries();

		foreach ($entries as $entry) {

			//start with the raw probability from he ODDs
			$score = $entry->getProbability();

			//roll 3 random d20 rolls (including subtracting) and add the results to the score - do actual 3 rolls make it more random?
			$score += mt_rand(-20, 20);
			$score += mt_rand(-20, 20);
			$score += mt_rand(-20, 20);

			//randomise the decimal
			$score += number_format(mt_rand() / mt_getrandmax(), 2);

			//did the horse have an incident during the race? (this should be quite rare)
			if (mt_rand(1, 200) <= 8) {
				$score = -1;
			}
			elseif ($score < 0) {
				$score = 0;
			}

			$entry->setScore($score);
		}

		//sort the results by score
		$iterator = $entries->getIterator();
		$iterator->uasort(function ($first, $second) {
			if ($first->getScore() === $second->getScore()) { return 0; }
			return $first->getScore() > $second->getScore() ? -1 : 1;
		});
		$finalPositions = new ArrayCollection(iterator_to_array($iterator));

		//loop through final placings
		$i = 1;
		foreach ($finalPositions as $entry) {
			$form = $i;
			if ($i > 9) { $form = 0; }

			//if the score is 0, we need to pick what the problem was
			if ($entry->getScore() === -1) {
				switch(mt_rand(1, 4)) {
					case 1:
						$form = 'S';
						break;
					case 2:
						$form = 'U';
						break;
					case 3:
						$form = 'P';
						break;
					case 4:
						$form = 'F';
						break;
				}
			}

			//update horse's form
			$horse = $entry->getHorse();
			$latestForm = $horse->getForm() . $form;
			if (strlen($latestForm) > 6) {
				$latestForm = substr($latestForm, 1);
			}
			$horse->setForm($latestForm);

			$this->em->persist($horse);
			$this->em->flush();

			$i++;
		}

		//save the race changes
		$this->em->persist($race);
		$this->em->flush();

		return true;
	}

}

/*
 * 1-9	the position the horse finished in a race
0	finished outside the top 9
P	pulled up (reined in as horse may be too tired/injured, or horse may just stop running)
F	fell
S	Slipped Up
R	refusal
B	brought down
U	unseated rider
-	separates years, i.e. left of this is from previous year, e.g. Dec 06 - Jan 07
/	separates racing seasons, i.e. left of this is from the season before last
 */