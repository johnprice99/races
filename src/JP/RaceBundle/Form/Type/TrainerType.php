<?php

namespace JP\RaceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TrainerType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', 'text')
			->add('level', 'text')
			->add('save', 'submit');
	}

	public function getName() {
		return 'trainer';
	}
}