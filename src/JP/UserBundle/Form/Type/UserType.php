<?php

namespace JP\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Role\RoleHierarchy;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType {

	private $securityContext;
	private $roles;

	public function __construct(SecurityContext $securityContext, RoleHierarchy $roles) {
		$this->securityContext = $securityContext;
		$this->roles = $roles;
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {
		//current logged in user
		$user = $this->securityContext->getToken()->getUser();

		$builder
			->add('email', 'text', array(
				'attr' => array(
					'class' => 'email',
					'minLength' => 5,
					'maxLength' => 255,
					'placeholder' => 'email@example.com',
					'data-msg-required' => 'Please enter an email address',
				)
			))
			->add('firstName', 'text', array(
				'attr' => array(
					'minLength' => 2,
					'maxLength' => 100,
					'data-msg-required' => 'Please enter a first name',
				)
			))
			->add('lastName', 'text', array(
				'attr' => array(
					'minLength' => 2,
					'maxLength' => 100,
					'data-msg-required' => 'Please enter a last name',
				)
			));

		if ($options['includePassword'] === true) {
			$builder->add('plainPassword', 'password', array(
				'label' => 'Password',
				'attr' => array(
					'minLength' => 8,
					'maxLength' => 16,
					'data-msg-required' => 'Please enter a password',
				),
			));
		}

		$builder
			->add('roles', 'choice', array(
				'expanded' => true,
				'required' => true,
				'multiple' => true,
				'choices' => $this->refactorRoles($options['roles'])
			))
			->add('locked', 'checkbox', array(
				'label' => 'Locked',
				'label_attr' => array(
					'class' => 'checkboxLabel',
				),
				'required' => false,
			));

		$builder->add('save', 'submit');
	}

	private function refactorRoles($originRoles) {
		$roles = array();

		// Add herited roles
		foreach ($originRoles as $roleParent => $rolesHerit) {
			foreach ($rolesHerit as $r) {
				$roles[$r] = ucwords(strtolower(substr($r, 5)));
			}
		}
		return $roles;
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'includePassword' => false,
			'roles' => null,
		));
	}

	public function getName() {
		return 'user';
	}
}