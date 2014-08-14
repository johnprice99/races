<?php

namespace JP\RaceBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadDefaultData implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface {

	private $container;

	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}

	public function load(ObjectManager $manager) {

		$userManager = $this->container->get('fos_user.user_manager');
		// Create our user and set details
		$user = $userManager->createUser();
		$user->setUsername('johnprice99@gmail.com');
		$user->setEmail('johnprice99@gmail.com');
		$user->setFirstName('John');
		$user->setLastName('Price');
		$user->setPlainPassword('myPass123');
		$user->setEnabled(true);
		$user->addRole('ROLE_ADMIN');

		// Update the user
		$userManager->updateUser($user, true);
	}

	public function getOrder() {
		return 1;
	}
}