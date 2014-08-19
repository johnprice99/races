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
	 * @ORM\Column(type="integer")
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
	 * @ORM\Column(type="integer")
	 */
	protected $stamina;

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

}