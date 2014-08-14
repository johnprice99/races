<?php

namespace JP\UserBundle\Tests\Controller;

use JP\RaceBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserControllerTest extends WebTestCase {

	private function truncateUserTable($container) {
		$connection = $container->get('doctrine')->getManager()->getConnection();
		$connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');
		$connection->executeUpdate($connection->getDatabasePlatform()->getTruncateTableSQL('user'));
		$connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');
	}

	public function testRegister() {
		$client = static::createClient();
		$container = $client->getContainer();
		$router = $container->get('router');

		//load up the registration page
		$crawler = $client->request('GET', $router->generate('fos_user_registration_register'));
		$this->assertCount(1, $crawler->filter('h1:contains("Register")'));

		//clear the test database user first (otherwise we will have conflicts as email not unique)
		$this->truncateUserTable($container);

		//fill out the form
		$form = $crawler->selectButton('Register')->form();
		$form['fos_user_registration_form[firstName]'] = 'Johnny';
		$form['fos_user_registration_form[lastName]'] = 'Test';
		$form['fos_user_registration_form[email]'] = 'johnny.test@gmail.com';
		$form['fos_user_registration_form[plainPassword][first]'] = 'myPass123';
		$form['fos_user_registration_form[plainPassword][second]'] = 'myPass123';
		$crawler = $client->submit($form);

		//check redirect to confirmation page
		$this->assertTrue($client->getResponse()->isRedirect());
		$crawler = $client->followRedirect();
		$this->assertCount(1, $crawler->filter('h1:contains("Account activated")'));

		//check redirect to account settings
		$link = $crawler->selectLink('Account settings')->link();
		$crawler = $client->click($link);
		$this->assertCount(1, $crawler->filter('h1:contains("Account settings")'));
	}

	public function testLogin() {
		$client = static::createClient();
		$container = $client->getContainer();
		$router = $container->get('router');

		//load up the login page
		$crawler = $client->request('GET', $router->generate('fos_user_security_login'));
		$this->assertCount(1, $crawler->filter('h1:contains("Log in")'));

		//load up the login form with fixtures data
		$form = $crawler->selectButton('_submit')->form();
		$form['_username'] = 'johnny.test@gmail.com';
		$form['_password'] = 'myPass123';
		$crawler = $client->submit($form);

		//now check that we have been redirected to the homepage
		$this->assertTrue($client->getResponse()->isRedirect($router->generate('homepage', array(), true)));
	}

	public function testLogout() {
		$client = static::createClient();
		$container = $client->getContainer();
		$router = $container->get('router');

		//populate the security context with a blank token
		$container->get('security.context')->setToken(new UsernamePasswordToken(new User(), null, 'main', array('ROLE_USER')));

		//load up the logout page
		$crawler = $client->request('GET', $router->generate('fos_user_security_logout'));

		//check that the user was removed
		$this->assertEmpty($container->get('security.context')->getToken());

		//now check that we have been redirected to the log in page
		$this->assertTrue($client->getResponse()->isRedirect($router->generate('fos_user_security_login', array(), true)));
	}
}