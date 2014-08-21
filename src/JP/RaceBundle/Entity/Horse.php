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
	 * @ORM\ManyToOne(targetEntity="Trainer", inversedBy="stable", cascade={"persist"})
	 * @ORM\JoinColumn(name="trainer_id", referencedColumnName="id")
	 */
	protected $trainer;

	/**
	 * @ORM\Column(type="smallint", options={"unsigned":true})
	 */
	protected $age;

	/**
	 * @ORM\Column(type="string", length=1)
	 */
	protected $sex;

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $available = 1;

	/**
	 * @ORM\Column(type="string", length=6)
	 */
	protected $form = '';

	/**
	 * @ORM\Column(type="string", length=4)
	 */
	protected $preferredType;

	/**
	 * @ORM\Column(type="smallint", options={"unsigned"=true})
	 */
	protected $stamina;

	/**
	 * @ORM\Column(type="smallint", options={"unsigned"=true})
	 */
	protected $behaviour;

	/**
	 * @ORM\Column(type="smallint", options={"unsigned"=true})
	 */
	protected $level;

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

	public function setTrainer($trainer) {
		$this->trainer = $trainer;
	}

	public function getTrainer() {
		return $this->trainer;
	}

	public function setAge($age) {
		$this->age = $age;
	}

	public function getAge() {
		return $this->age;
	}

	public function setSex($sex) {
		$this->sex = $sex;
	}

	public function getSex() {
		return $this->sex;
	}

	public function setAvailable($available) {
		$this->available = $available;
	}

	public function getAvailable() {
		return $this->available;
	}

	public function setForm($form) {
		$this->form = $form;
	}

	public function getForm() {
		return $this->form;
	}

	public function setPreferredType($preferredType) {
		$this->preferredType = $preferredType;
	}

	public function getPreferredType() {
		return $this->preferredType;
	}

	public function setStamina($stamina) {
		$this->stamina = $stamina;
	}

	public function getStamina() {
		return $this->stamina;
	}

	public function setBehaviour($behaviour) {
		$this->behaviour = $behaviour;
	}

	public function getBehaviour() {
		return $this->behaviour;
	}

	public function setLevel($level) {
		$this->level = $level;
	}

	public function getLevel() {
		return $this->level;
	}

	public function generateLevel() {
		//horses level generated from stamina, behaviour and age
		//100 stamina + 9 behaviour + 6/7 = 100  class 1 - levels 7, 8, 9 class 2 - level 4, 5, 6, 7, class 5 - level 1, 2, 3
		$level = 100;
		$level -= 100 - (10 * $this->getBehaviour());
		$level -= round($this->getStamina(), -1) / 10;
		switch ($this->getAge()) {
			case 7:
			case 8:
				$level += 5; //best age
				break;
			case 6:
			case 9:
				$level += 5; // maturing / declining
				break;
		}

		return $this->setLevel(round($level, -1) / 10);
	}

}