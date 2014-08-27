<?php

namespace JP\RaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="jockey")
 * @ORM\Entity(repositoryClass="JP\RaceBundle\Entity\Repository\JockeyRepository")
 */
class Jockey extends Person {

	/**
	 * @ORM\Column(type="decimal", precision=4, scale=2)
	 */
	protected $weight;

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $available = 1;

	public function setWeight($weight) {
		$this->weight = $weight;
	}

	public function getWeight() {
		//stored in database as 10.30 - needs to be displayed as 10-3
		return substr(str_replace('.', '-', $this->weight), 0, -1);
	}

	public function getNumericWeight() {
		return $this->weight;
	}

	public function setAvailable($available) {
		$this->available = $available;
	}

	public function getAvailable() {
		return $this->available;
	}

}