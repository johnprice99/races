<?php

namespace JP\RaceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use JP\RaceBundle\Form\Type\GenerateRaceType;
use JP\RaceBundle\Form\Type\GenerateNumberType;
use JP\RaceBundle\Form\Type\GenerateTrainerType;
use JP\RaceBundle\Entity\Owner;
use JP\RaceBundle\Entity\Trainer;
use JP\RaceBundle\Entity\Jockey;
use JP\RaceBundle\Entity\Horse;

/**
 * @Route("/admin")
 */
class AdminController extends Controller {

	/**
	 * @Route("/generate/race/single", name="generate_single_race")
	 * @Template("JPRaceBundle:Admin:generate.html.twig")
	 */
	public function generateSingleRaceAction(Request $request) {
		$form = $this->createForm(new GenerateRaceType());
		$form->handleRequest($request);

		if ($form->isValid()) {
			$this->get('jp.race.generator')->generate($form->getData());
			$this->get('session')->getFlashBag()->add('success', 'Race has been generated');
			return $this->redirect($this->generateUrl('generate_single_race'));
		}

		return array(
			'form' => $form->createView(),
			'genTitle' => 'New race',
		);
	}

	/**
	 * @Route("/generate/races/all", name="generate_all_races")
	 * @Template("JPRaceBundle:Admin:generate.html.twig")
	 */
	public function generateAllRacesAction(Request $request) {
		$form = $this->createForm(new GenerateNumberType());
		$form->handleRequest($request);

		if ($form->isValid()) {
			$data = $form->getData();
			for ($i = 1; $i <= $data['number']; $i++) {

				$options = array(
					'runners' => mt_rand(5, 15),
					'class' => mt_rand(1, 3),
				);

				switch(mt_rand(1, 3)) {
					case 1:
						$options['type'] = 'flat';
						break;
					case 2:
						$options['type'] = 'fence';
						break;
					case 3:
						$options['type'] = 'hurdle';
						break;
				}

				$this->get('jp.race.generator')->generate($options);
			}
			$this->get('session')->getFlashBag()->add('success', 'Today\'s races have been gemerated');
			return $this->redirect($this->generateUrl('race_list'));
		}

		return array(
			'form' => $form->createView(),
			'genTitle' => 'New race',
		);
	}

	/**
	 * @Route("/generate/jockey", name="generate_jockey")
	 * @Template("JPRaceBundle:Admin:generate.html.twig")
	 */
	public function generateJockeyAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$db = $this->get('database_connection');

		$form = $this->createForm(new GenerateNumberType());
		$form->handleRequest($request);

		if ($form->isValid()) {
			$data = $form->getData();
			for ($i = 1; $i <= $data['number']; $i++) {
				$jockey = new Jockey();

				//Generate a random name
				$firstInitial = chr(mt_rand(ord('A'), ord('Z')));
				$name = $db->fetchAssoc('SELECT * FROM `available_names` ORDER BY rand() LIMIT 1');
				$jockey->setName($firstInitial . '. ' . $name['value']);

				$weight = mt_rand(8, 12) . '.' . mt_rand(0, 14);
				$jockey->setWeight($weight);
				$jockey->setLevel(mt_rand(50, 95));
				$jockey->setAvailable(true);

				$em->persist($jockey);
				$em->flush();
			}

			$this->get('session')->getFlashBag()->add('success', $data['number'] . ' new Jockeys have been created');
			return $this->redirect($this->generateUrl('jockey_list'));
		}

		return array(
			'form' => $form->createView(),
			'genTitle' => 'jockeys',
		);
	}

	/**
	 * @Route("/generate/trainer", name="generate_trainer")
	 * @Template("JPRaceBundle:Admin:generate.html.twig")
	 */
	public function generateTrainerAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$db = $this->get('database_connection');

		$form = $this->createForm(new GenerateTrainerType());
		$form->handleRequest($request);

		if ($form->isValid()) {
			$data = $form->getData();
			for ($i = 1; $i<= $data['number']; $i++) {
				$trainer = new Trainer();

				//Generate a random name
				$firstInitial = chr(mt_rand(ord('A'), ord('Z')));
				$name = $db->fetchAssoc('SELECT * FROM `available_names` ORDER BY rand() LIMIT 1');
				$trainer->setName($firstInitial . '. ' . $name['value']);
				$trainer->setLevel(mt_rand(50, 95));

				$em->persist($trainer);
				$em->flush();
			}

			$this->get('session')->getFlashBag()->add('success', $data['number'] . ' new Trainers have been created');
			return $this->redirect($this->generateUrl('trainer_list'));
		}

		return array(
			'form' => $form->createView(),
			'genTitle' => 'trainers',
		);
	}

	/**
	 * @Route("/generate/owner", name="generate_owner")
	 * @Template("JPRaceBundle:Admin:generate.html.twig")
	 */
	public function generateOwnerAction(Request $request) {
		$em = $this->getDoctrine()->getManager();
		$db = $this->get('database_connection');

		$form = $this->createForm(new GenerateTrainerType());
		$form->handleRequest($request);

		if ($form->isValid()) {

			$possibleColours = array('red', 'white', 'green', 'black', 'yellow', 'orange', 'purple', 'blue', 'brown', 'grey', 'black');
			$possibleStyles = array(
				'helmet' => array('plain', 'stripes', 'radialStripes', 'dot', 'star', 'spots', 'quartered', 'diamond', 'halfVertical'),
				'jersey' => array('plain', 'stripeHigh', 'stripeMiddle', 'stripeLow', 'stripeLeftRight', 'stripeRightLeft', 'stripeCross', 'halfVertical', 'halfHorizontal', 'hoops', 'stripes', 'radialStripes', 'dot', 'star', 'diamond', 'spots', 'chevron', 'bib', 'quarterCross', 'quartered'),
				'sleeve' => array('plain', 'hoops', 'stripes', 'stripeHigh', 'stripeMiddle', 'stripeLow'),
			);

			$data = $form->getData();
			for ($i = 1; $i<= $data['number']; $i++) {
				$owner = new Owner();

				//Generate a random name
				$firstInitial = chr(mt_rand(ord('A'), ord('Z')));
				$name = $db->fetchAssoc('SELECT * FROM `available_names` ORDER BY rand() LIMIT 1');
				$owner->setName($firstInitial . '. ' . $name['value']);
				$owner->setLevel(mt_rand(50, 95));

				$owner->setBaseColor($possibleColours[array_rand($possibleColours)]);
				$owner->setDecorationColor($possibleColours[array_rand($possibleColours)]);
				$owner->setHelmetStyle($possibleStyles['helmet'][array_rand($possibleStyles['helmet'])]);
				$owner->setJerseyStyle($possibleStyles['jersey'][array_rand($possibleStyles['jersey'])]);
				$owner->setSleeveStyle($possibleStyles['sleeve'][array_rand($possibleStyles['sleeve'])]);

				if ($data['generateHorses']) {
					$horsesInStable = mt_rand(1, 5);
					for ($h = 1; $h <= $horsesInStable; $h++) {
						$owner->addHorse($this->createHorse($db));
					}
				}

				$em->persist($owner);
				$em->flush();
			}

			$this->get('session')->getFlashBag()->add('success', $data['number'] . ' new Owners have been created');
			return $this->redirect($this->generateUrl('owner_list'));
		}

		return array(
			'form' => $form->createView(),
			'genTitle' => 'owners',
		);
	}

	/**
	 * @Route("/generate/horse/{trainerID}", name="generate_horse")
	 * @Template("JPRaceBundle:Admin:generate.html.twig")
	 */
	public function generateHorseAction($trainerID, Request $request) {
		$em = $this->getDoctrine()->getManager();
		$db = $this->get('database_connection');

		$trainer = $em->getRepository('JPRaceBundle:Trainer')->find($trainerID);

		if (!$trainer) {
			throw $this->createNotFoundException('Trainer not found');
		}

		$form = $this->createForm(new GenerateNumberType());
		$form->handleRequest($request);

		if ($form->isValid()) {
			$data = $form->getData();

			for ($i = 1; $i<= $data['number']; $i++) {
				$trainer->addHorse($this->createHorse($db));
			}

			$em->persist($trainer);
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', $data['number'] . ' horses have been added to '. $trainer->getName());
			return $this->redirect($this->generateUrl('trainer_view', array('id' => $trainerID)));
		}

		return array(
			'form' => $form->createView(),
			'genTitle' => 'horses',
		);
	}

	private function createHorse($db) {
		//pick a random trainer for this horse
		$em = $this->getEntityManager();
		$max = $em->createQuery('SELECT MAX(t.id) FROM JPRaceBundle:Trainer t')->getSingleScalarResult();
		echo $max;
		die();
		return $em->createQuery('
            SELECT q FROM EnzimQuestionBundle:Question q
            WHERE q.id >= :rand
            ORDER BY q.id ASC
            ')
			->setParameter('rand',rand(0,$max))
			->setMaxResults(1)
			->getSingleResult();



		$horse = new Horse();

		$row = $db->fetchAssoc('SELECT * FROM `horse_names` ORDER BY rand() LIMIT 1');
		//now remove the name so that it doesn't get picked again
		$db->delete('horse_names', array('id' => $row['id']));
		$horse->setName($row['value']);
		$sex = (mt_rand(1, 2) == 2) ? 'm' : 'f';
		$horse->setSex($sex);
//		$horse->setAge(mt_rand(2, 11)); used for when we generate 2 year old horses for nursery races
		$horse->setAge(mt_rand(3, 11));
		$horse->setAvailable(true);
		if ($sex === 'm') {
			$horse->setGelded(mt_rand(0, 1));
		}
		elseif ($horse->getAge() > 3) { //don't breed horses younger than 4
			$horse->setBred(mt_rand(0, 1));
		}

		switch (mt_rand(1, 3)) {
			case 1:
				$horse->setPreferredType('flat');
				break;
			case 2:
				$horse->setPreferredType('hurdle');
				break;
			case 3:
				$horse->setPreferredType('fence');
				break;
		}

		$horse->setStamina(mt_rand(50, 100));
		$horse->setBehaviour(mt_rand(6, 9));
		$horse->generateLevel();

		return $horse;
	}
}
