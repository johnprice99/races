<?php

namespace JP\RaceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/help")
 */
class HelpController extends Controller {

	/**
	 * @Route("/{path}", name="help_page")
	 */
	public function helpAction($path = 'index') {
		try {
			return $this->render('JPRaceBundle:Help:' . $path . '.html.twig');
		}
		catch (\Exception $ex) {
			throw $this->createNotFoundException('Help file: ' . $path . ' does not exist');
		}
	}

}
