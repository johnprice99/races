<?php

namespace JP\RaceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class HorseType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', 'text')
			->add('age', 'text')
			->add('sex', 'choice', array(
				'choices' => array('m' => 'Male', 'f' => 'Female')
			))
			->add('available', 'checkbox', array(
				'required' => false,
			))
			->add('form', 'text', array(
				'required' => false,
			))
			->add('preferredType', 'choice', array(
				'choices' => array('flat' => 'Flat', 'hurdle' => 'Hurdle', 'fence' => 'Fence')
			))
			->add('stamina', 'text')
			->add('behaviour', 'text')
			->add('level', 'text')
			->add('flatMaiden', 'checkbox', array(
				'required' => false,
			))
			->add('jumpMaiden', 'checkbox', array(
				'required' => false,
			))
			->add('save', 'submit');
	}

	public function getName() {
		return 'horse';
	}
}