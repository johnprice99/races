<?php

namespace JP\RaceBundle\Service;

use Doctrine\ORM\EntityManager;
use JP\RaceBundle\Entity\Race;

class RaceEngine {

	private $em;

	public function __construct(EntityManager $entityManager) {
		$this->em = $entityManager;
	}

	public function run(Race $race) {
		$entries = $race->getEntries();

		$finalPositions = $entries->toArray();
		shuffle($finalPositions);

		foreach ($finalPositions as $i => $e) {
			echo $i+1 . ': ' . $e->getHorse()->getName()."<br />";
		}

		//$winner = $entries->get(array_rand($entries->toArray()));

		//update the horses last positions (see below)

//		print_r($winner->getHorse()->getName());
		die();

		//add in random events (like not finished etc)
		//set the horses as available
		//remove the race and all entrants

		//save the race changes
//		$this->em->persist($race);
//		$this->em->flush();

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