<?php

namespace JP\RaceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PageController extends Controller {

	/**
	 * @Route("/", name="homepage")
	 * @Template()
	 */
	public function indexAction() {
		$user = $this->getUser();

		return array();
	}
}
