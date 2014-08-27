<?php

namespace JP\RaceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class JockeyType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', 'text')
			->add('level', 'text')
			->add('weight', 'text')
			->add('available', 'checkbox', array(
				'required' => false,
			))
			->add('save', 'submit');
	}

	public function getName() {
		return 'jockey';
	}
}