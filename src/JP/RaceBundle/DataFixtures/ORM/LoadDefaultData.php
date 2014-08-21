<?php

namespace JP\RaceBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use JP\RaceBundle\Entity\Trainer;
use JP\RaceBundle\Entity\Jockey;
use JP\RaceBundle\Entity\Horse;

class LoadDefaultData implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface {

	private $container;

	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}

	public function load(ObjectManager $manager) {
		$db = $this->container->get('database_connection');

		echo "Loading Names Database...\n\n";
		//create the horse names database
		$db->executeUpdate('CREATE TABLE if not exists `available_names` (`id` int(11) NOT NULL AUTO_INCREMENT, `value` varchar(20) COLLATE utf8_unicode_ci NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
		$db->executeUpdate('TRUNCATE `available_names`');
		$path = $this->container->get('kernel')->locateResource('@JPRaceBundle/DataFixtures/names.txt');
		//open up names text file and load each into the database
		$handle = fopen($path, "r") or die("Unable to open names file!");
		while (!feof($handle)) {
			$db->insert('available_names', array('value' => trim(fgets($handle))));
		}

		echo "Loading Horse Names Database...\n\n";
		//create the horse names database
		$db->executeUpdate('CREATE TABLE if not exists `horse_names` (`id` int(11) NOT NULL AUTO_INCREMENT, `value` varchar(35) COLLATE utf8_unicode_ci NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
		$db->executeUpdate('TRUNCATE `horse_names`');
		$path = $this->container->get('kernel')->locateResource('@JPRaceBundle/DataFixtures/horses.txt');
		//open up names text file and load each into the database
		$handle = fopen($path, "r") or die("Unable to open horse names file!");
		while (!feof($handle)) {
			$db->insert('horse_names', array('value' => trim(fgets($handle))));
		}

		echo "Creating Trainers...\n\n";
		for ($i = 1; $i <= 200; $i++) {
			$trainer = new Trainer();

			//Generate a random name
			$firstInitial = chr(mt_rand(ord('A'), ord('Z')));
			$name = $db->fetchAssoc('SELECT * FROM `available_names` ORDER BY rand() LIMIT 1');
			$trainer->setName($firstInitial . '. ' . $name['value']);
			$trainer->setLevel(mt_rand(50, 95));

			$horsesInStable = mt_rand(1, 5);
			for ($h = 1; $h <= $horsesInStable; $h++) {
				$horse = new Horse();

				$row = $db->fetchAssoc('SELECT * FROM `horse_names` ORDER BY rand() LIMIT 1');
				//now remove the name so that it doesn't get picked again
				$db->delete('horse_names', array('id' => $row['id']));
				$horse->setName($row['value']);
				$sex = (mt_rand(1, 2) == 2) ? 'm' : 'f';
				$horse->setSex($sex);
				$horse->setAge(mt_rand(3, 11));
				$horse->setAvailable(true);
				$typePref = (mt_rand(1, 2) == 2) ? 'flat' : 'jump';
				$horse->setPreferredType($typePref);
				$horse->setStamina(mt_rand(50, 100));
				$horse->setBehaviour(mt_rand(6, 9));
				$horse->generateLevel();

				$trainer->addHorse($horse);
			}

			$manager->persist($trainer);
			$manager->flush();
		}

		echo "Creating Jockeys...\n\n";
		for ($i = 1; $i <= 500; $i++) {
			$jockey = new Jockey();

			//Generate a random name
			$firstInitial = chr(mt_rand(ord('A'), ord('Z')));
			$name = $db->fetchAssoc('SELECT * FROM `available_names` ORDER BY rand() LIMIT 1');
			$jockey->setName($firstInitial . '. ' . $name['value']);

			$weight = mt_rand(8, 12) . '.' . mt_rand(0, 14);
			$jockey->setWeight($weight);
			$jockey->setLevel(mt_rand(50, 95));
			$jockey->setAvailable(true);

			$manager->persist($jockey);
			$manager->flush();
		}

		echo "Loading Default User...\n\n";
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