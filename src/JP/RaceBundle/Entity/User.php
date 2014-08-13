<?php

namespace JP\RaceBundle\Entity;

use JP\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
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

	public function setBalance($balance) {
		$this->balance = $balance;
	}

	public function getBalance() {
		return $this->balance;
	}

}