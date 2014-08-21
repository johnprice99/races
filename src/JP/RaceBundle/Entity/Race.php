<?php

namespace JP\RaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="race")
 */
class Race {

	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\OneToMany(targetEntity="Entry", mappedBy="race", cascade={"persist"})
	 */
	protected $entries;

	/**
	 * @ORM\Column(type="smallint", options={"unsigned"=true})
	 */
	private $class;

	/**
	 * @ORM\Column(type="smallint", options={"unsigned"=true})
	 */
	private $runnerCount;

	/**
	 * @ORM\Column(type="string", length=4)
	 */
	private $type;

	/**
	 * @ORM\Column(type="smallint", options={"unsigned"=true})
	 */
	private $minAge;

	/**
	 * @ORM\Column(type="smallint", options={"unsigned"=true})
	 */
	private $distance;

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $maiden;

	public function __construct() {
		$this->runnerCount = mt_rand(5, 15);
		$this->setClass(mt_rand(1, 3));
		$this->entries = new ArrayCollection();
		$this->type = (mt_rand(1, 2) == 2) ? 'flat' : 'jump';
		$this->minAge = mt_rand(3, 5);
		$this->maiden = mt_rand(0, 1);

		if ($this->type == 'flat') {
			//if flat
			$this->distance = mt_rand(1100, 3899);
		}
		else {
			//if jump
			$this->distance = mt_rand(3520, 7920);
		}
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}

	public function setEntries($entries) {
		$this->entries = $entries;
	}

	public function getEntries() {
		return $this->entries;
	}

	public function addEntry($entry) {
		$entry->setRace($this);
		$this->entries->add($entry);
	}

	public function setClass($class) {
		$this->class = $class;
	}

	public function getClass() {
		return $this->class;
	}

	public function getRunnerCount() {
		return $this->runnerCount;
	}

	public function setType($type) {
		$this->type = $type;
	}

	public function getType() {
		return $this->type;
	}

	public function setMinAge($minAge) {
		$this->minAge = $minAge;
	}

	public function getMinAge() {
		return $this->minAge;
	}

	public function setDistance($distance) {
		$this->distance = $distance;
	}

	public function getDistance() {
		return $this->distance;
	}

	public function setMaiden($maiden) {
		$this->maiden = $maiden;
	}

	public function getMaiden() {
		return $this->maiden;
	}
}