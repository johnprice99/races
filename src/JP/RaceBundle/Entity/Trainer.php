<?php

namespace JP\RaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="trainer")
 * @ORM\Entity(repositoryClass="JP\RaceBundle\Entity\Repository\TrainerRepository")
 */
class Trainer extends Person {

	/**
	 * @ORM\OneToMany(targetEntity="Horse", mappedBy="trainer", cascade={"persist"})
	 */
	protected $stable;

	public function __construct() {
		$this->stable = new ArrayCollection();
	}

	public function setStable($stable) {
		$this->stable = $stable;
	}

	public function getStable() {
		return $this->stable;
	}

	public function addHorse($horse) {
		$horse->setTrainer($this);
		$this->stable->add($horse);
	}

}