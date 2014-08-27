<?php

namespace JP\RaceBundle\Entity;

use JP\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser {

	/**
	 * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=true)
	 */
	protected $balance = 10000;

	/**
	 * @ORM\OneToMany(targetEntity="Bet", mappedBy="user")
	 */
	protected $bets;

	public function __construct() {
		$this->bets = new ArrayCollection();
	}

	public function setBalance($balance) {
		$this->balance = $balance;
	}

	public function getBalance() {
		return $this->balance;
	}

	public function setBets($bets) {
		$this->bets = $bets;
	}

	public function getBets() {
		return $this->bets;
	}

	public function addBet($bet) {
		$bet->setUser($this);
		$this->bets->add($bet);
	}

}