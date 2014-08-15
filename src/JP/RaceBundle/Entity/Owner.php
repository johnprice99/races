<?php

namespace JP\RaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="owner")
 */
class Owner extends Person {

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
		$horse->setOwner($this);
		$this->stable->add($horse);
	}

}