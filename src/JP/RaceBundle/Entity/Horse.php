<?php

namespace JP\RaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="horse")
 */
class Horse {

	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", length=35)
	 */
	protected $name;

	/**
	 * @ORM\ManyToOne(targetEntity="Owner", inversedBy="stable", cascade={"persist"})
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
	 */
	protected $owner;

	public function setId($id) {
		$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getName() {
		return $this->name;
	}

	public function setOwner($owner) {
		$this->owner = $owner;
	}

	public function getOwner() {
		return $this->owner;
	}

}