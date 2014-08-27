<?php

namespace JP\RaceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class GenerateNumberType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('number', 'number')
			->add('generate', 'submit');
	}

	public function getName() {
		return 'generateNumber';
	}
}