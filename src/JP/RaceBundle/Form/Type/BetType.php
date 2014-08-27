<?php

namespace JP\RaceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BetType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('stake', 'number')
			->add('placeBet', 'submit');
	}

	public function getName() {
		return 'bet';
	}
}