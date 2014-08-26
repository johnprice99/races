<?php

namespace JP\RaceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class GenerateRaceType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$possibleRunners = array();
		for ($i = 5; $i <= 15; $i++) {
			$possibleRunners[$i] = $i;
		}

		$builder
			->add('runners', 'choice', array(
				'choices' => $possibleRunners,
			))
			->add('class', 'choice', array(
				'choices' => array(1 => 1, 2 => 2, 3 => 3),
			))
			->add('type', 'choice', array(
				'choices' => array(
					'flat' => 'Flat',
					'hurdle' => 'Hurdles',
					'fence' => 'Fences',
				),
			))
			->add('generate', 'submit');
	}

	public function getName() {
		return 'generateRace';
	}
}