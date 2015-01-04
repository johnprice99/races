<?php

namespace JP\RaceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/credits")
 */
class CreditController extends Controller {

	/**
	 * @Route("/purchase", name="purchase_credits")
	 */
	public function purchaseCreditsAction() {
		$this->getUser()->addCredits(1000);
		$em = $this->getDoctrine()->getManager();
		$em->persist($this->getUser());
		$em->flush();
		$this->get('session')->getFlashBag()->add('success', '1000cr has been added to your account.');

		return $this->redirect($this->generateUrl('homepage'));
	}

}
