<?php

namespace JP\RaceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="owner")
 * @ORM\Entity(repositoryClass="JP\RaceBundle\Entity\Repository\OwnerRepository")
 */
class Owner extends Person {

	/**
	 * @ORM\Column(type="string", length=20)
	 */
	protected $baseColor;

	/**
	 * @ORM\Column(type="string", length=20)
	 */
	protected $decorationColor;

	/**
	 * @ORM\Column(type="string", length=20)
	 */
	protected $helmetStyle;

	/**
	 * @ORM\Column(type="string", length=20)
	 */
	protected $jerseyStyle;

	/**
	 * @ORM\Column(type="string", length=20)
	 */
	protected $sleeveStyle;

	/**
	 * @ORM\OneToMany(targetEntity="Horse", mappedBy="owner", cascade={"persist"})
	 */
	protected $stable;

	public function __construct() {
		$this->stable = new ArrayCollection();
	}

	public function setBaseColor($baseColor) {
		$this->baseColor = $baseColor;
	}

	public function getBaseColor() {
		return $this->baseColor;
	}

	public function setDecorationColor($decorationColor) {
		$this->decorationColor = $decorationColor;
	}

	public function getDecorationColor() {
		return $this->decorationColor;
	}

	public function setHelmetStyle($helmetStyle) {
		$this->helmetStyle = $helmetStyle;
	}

	public function getHelmetStyle() {
		return $this->helmetStyle;
	}

	public function setJerseyStyle($jerseyStyle) {
		$this->jerseyStyle = $jerseyStyle;
	}

	public function getJerseyStyle() {
		return $this->jerseyStyle;
	}

	public function setSleeveStyle($sleeveStyle) {
		$this->sleeveStyle = $sleeveStyle;
	}

	public function getSleeveStyle() {
		return $this->sleeveStyle;
	}

	public function setStable($stable) {
		$this->stable = $stable;
	}

	public function getStable() {
		return $this->stable;
	}

	public function addHorse($horse) {
		$horse->setOwner($this);
		$this->stable->add($horse);
	}

}