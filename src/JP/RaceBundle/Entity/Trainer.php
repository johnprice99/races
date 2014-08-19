<?php

namespace JP\RaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="trainer")
 */
class Trainer extends Person {

	/**
	 * @ORM\OneToMany(targetEntity="Horse", mappedBy="category", cascade={"persist"})
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