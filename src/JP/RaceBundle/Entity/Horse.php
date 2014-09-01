<?php

namespace JP\RaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="horse")
 * @ORM\Entity(repositoryClass="JP\RaceBundle\Entity\Repository\HorseRepository")
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

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $flatMaiden = 1;

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $jumpMaiden = 1;

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $bred = 0;

	/**
	 * @ORM\Column(type="boolean")
	 */
	protected $gelded = 0;

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
		//class 1 - levels 7, 8, 9 class 2 - level 4, 5, 6, 7, class 5 - level 1, 2, 3
		$level = 11;
		$level -= 10 - $this->behaviour;
		$level -= 10 - round($this->stamina, -1) / 10;

		if ($this->age < 7) {
			$level -= 7 - $this->age;
		}
		elseif ($this->age <= 8) {
			$level += 2;
		}
		elseif ($this->age == 10) {
			$level--;
		}
		elseif ($this->age > 10) {
			$level -= $this->age - 10;
		}

		//sanity check
		if ($level > 10) {
			$level = 10;
		}
		elseif ($level < 1) {
			$level = 1;
		}

		return $this->setLevel($level);
	}

	public function setFlatMaiden($flatMaiden) {
		$this->flatMaiden = $flatMaiden;
	}

	public function getFlatMaiden() {
		return $this->flatMaiden;
	}

	public function setJumpMaiden($jumpMaiden) {
		$this->jumpMaiden = $jumpMaiden;
	}

	public function getJumpMaiden() {
		return $this->jumpMaiden;
	}

	public function setBred($bred) {
		$this->bred = $bred;
	}

	public function getBred() {
		return $this->bred;
	}

	public function setGelded($gelded) {
		$this->gelded = $gelded;
	}

	public function getGelded() {
		return $this->gelded;
	}

	public function getDescription() {
		if ($this->sex === 'm') {
			if ($this->gelded) {
				return 'gelding';
			}
			if ($this->age < 5) {
				return 'colt';
			}
			return 'horse';
		}
		else {
			if ($this->bred || $this->age >= 5) {
				return 'mare';
			}
			return 'filly';
		}
	}

}