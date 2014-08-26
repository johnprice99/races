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
	 * @ORM\JoinColumn(name="race_id", referencedColumnName="id", onDelete="CASCADE")
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

	/**
	 * @ORM\Column(type="smallint", options={"unsigned"=true}, nullable=true)
	 */
	protected $score;

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	protected $favourite;

	/**
	 * @ORM\Column(type="smallint", options={"unsigned"=true}, nullable=true)
	 */
	protected $result;

	/**
	 * @ORM\Column(type="smallint", options={"unsigned"=true}, nullable=true)
	 */
	protected $finalPosition;

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

	public function setScore($score) {
		$this->score = $score;
	}

	public function getScore() {
		return $this->score;
	}

	public function setFavourite($favourite) {
		$this->favourite = $favourite;
	}

	public function getFavourite() {
		return $this->favourite;
	}

	public function setResult($result) {
		$this->result = $result;
	}

	public function getResult() {
		return $this->result;
	}

	public function setFinalPosition($finalPosition) {
		$this->finalPosition = $finalPosition;
	}

	public function getFinalPosition() {
		return $this->finalPosition;
	}

}