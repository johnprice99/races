<?php

namespace JP\RaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="bet")
 */
class Bet {

	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Entry", inversedBy="bets")
	 * @ORM\JoinColumn(name="entry_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $entry;

	/**
	 * @ORM\ManyToOne(targetEntity="Race", inversedBy="bets")
	 * @ORM\JoinColumn(name="race_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $race;

	/**
	 * @ORM\ManyToOne(targetEntity="Horse")
	 * @ORM\JoinColumn(name="horse_id", referencedColumnName="id")
	 */
	protected $horse;

	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="bets")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $user;

	/**
	 * @ORM\Column(type="integer", options={"unsigned"=true})
	 */
	protected $stake;

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

	public function setEntry($entry) {
		$this->entry = $entry;
	}

	public function getEntry() {
		return $this->entry;
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

	public function setUser($user) {
		$this->user = $user;
	}

	public function getUser() {
		return $this->user;
	}

	public function setStake($stake) {
		$this->stake = $stake;
	}

	public function getStake() {
		return $this->stake;
	}

	public function setOdds($odds) {
		$this->odds = $odds;
	}

	public function getOdds() {
		return $this->odds;
	}

}