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

			//start with the score from the ODDs
			$result = $entry->getScore();

			//roll 3 random d20 rolls (including subtracting) and add the results to the score - do actual 3 rolls make it more random?
			$result += mt_rand(-20, 20);
			$result += mt_rand(-20, 20);
			$result += mt_rand(-20, 20);

			//adjust result based on latest result (replicate morale)
			$form = substr($entry->getHorse()->getForm(), -1);
			if (is_numeric($form) && $form != 0) {
				$result += 10 - $form;
			}
			elseif(is_numeric($form)) {
				$result -= $form;
			}
			else {
				$result -= 10;
			}

			//randomise the decimal
			$result += number_format(mt_rand() / mt_getrandmax(), 2);

			//did the horse have an incident during the race? (this should be quite rare)
			if (mt_rand(1, 200) <= 8) {
				$result = -1;
			}
			elseif(mt_rand(1, 10) > $entry->getHorse()->getBehaviour()) { //check the behaviour of the horse to see if it refused to run
				//now, roll again to see if the trainer and jockey managed to get the horse to run
				if (mt_rand(1, 10) < 5) {
					$result = -2;
				}
			}
			elseif ($result < 0) {
				$result = 0;
			}

			$entry->setResult($result);
		}

		//sort the results by result
		$iterator = $entries->getIterator();
		$iterator->uasort(function ($first, $second) {
			if ($first->getResult() === $second->getResult()) { return 0; }
			return $first->getResult() > $second->getResult() ? -1 : 1;
		});
		$finalPositions = new ArrayCollection(iterator_to_array($iterator));

		//loop through final placings
		$i = 1;
		foreach ($finalPositions as $entry) {
			$entry->setFinalPosition($i);

			$form = $i;
			if ($i > 9) { $form = 0; }

			//if the score is 0, we need to pick what the problem was
			if ($entry->getResult() === -1) {
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
//				$entry->setFinalPosition(0);
			}
			elseif ($entry->getResult() === -2) {
				$form = 'R';
//				$entry->setFinalPosition(0);
			}

			//update horse's form
			$horse = $entry->getHorse();
			if ($i === 1) {
				if ($race->getType() == 'flat') {
					$horse->setFlatMaiden(false);
				}
				elseif ($race->getType() == 'jump') {
					$horse->setJumpMaiden(false);
				}
			}
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