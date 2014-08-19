<?php

namespace JP\RaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="entry")
 */
class Entry {

	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Race", inversedBy="entries")
	 * @ORM\JoinColumn(name="race_id", referencedColumnName="id")
	 */
	protected $race;

	/**
	 * @ORM\OneToOne(targetEntity="Horse")
	 */
	protected $horse;

	/**
	 * @ORM\OneToOne(targetEntity="Jockey")
	 */
	protected $jockey;

	/**
	 * @ORM\Column(type="string", length=6)
	 */
	protected $odds;

	public function setId($id) {
		$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}

	public function setRace($race) {
		$this->race = $race;
	}

	public function getRace() {
		return $this->race;
	}

	public function setHorse($horse) {
		$this->horse = $horse;
	}

	public function getHorse() {
		return $this->horse;
	}

	public function setJockey($jockey) {
		$this->jockey = $jockey;
	}

	public function getJockey() {
		return $this->jockey;
	}

	public function setOdds($odds) {
		$this->odds = $odds;
	}

	public function getOdds() {
		return $this->odds;
	}

}