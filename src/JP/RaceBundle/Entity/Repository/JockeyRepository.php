<?php

namespace JP\RaceBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class JockeyRepository extends EntityRepository {

	public function findAll() {
		return $this->findBy(array(), array('name' => 'ASC'));
	}
}