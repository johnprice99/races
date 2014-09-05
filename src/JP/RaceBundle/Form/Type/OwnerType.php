<?php

namespace JP\RaceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class OwnerType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', 'text')
			->add('baseColour', 'text')
			->add('decorationColor', 'text')
			->add('helmetStyle', 'text')
			->add('jerseyStyle', 'text')
			->add('sleeveStyle', 'text')
			->add('save', 'submit');
	}

	public function getName() {
		return 'owner';
	}
}