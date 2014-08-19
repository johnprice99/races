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
	 * @ORM\Column(type="integer")
	 */
	private $runnerCount;

	/**
	 * @ORM\Column(type="string", length=4)
	 */
	private $type;

	public function __construct() {
		$this->runnerCount = mt_rand(5, 15);
		$this->entries = new ArrayCollection();
		$this->type = (mt_rand(1, 2) == 2) ? 'flat' : 'jump';
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

	public function getRunnerCount() {
		return $this->runnerCount;
	}

	public function setType($type) {
		$this->type = $type;
	}

	public function getType() {
		return $this->type;
	}

}