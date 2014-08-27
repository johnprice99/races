<?php

namespace JP\RaceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use JP\RaceBundle\Entity\Bet;
use JP\RaceBundle\Form\Type\BetType;

/**
 * @Route("/bet")
 */
class BetController extends Controller {

	/**
	 * @Route("/list", name="bet_list")
	 */
	public function listAction() {
		echo 'list';
		die();
	}

	/**
	 * @Route("/{entryID}", name="bet_place")
	 * @Template()
	 */
	public function placeAction($entryID, Request $request) {
		$em = $this->getDoctrine()->getManager();

		$entry = $em->getRepository('JPRaceBundle:Entry')->find($entryID);

		if (!$entry) {
			throw $this->createNotFoundException('Race entry not found');
		}

		$bet = new Bet();
		$bet->setEntry($entry);
		$bet->setOdds($entry->getOdds());
		$bet->setUser($this->getUser());

		$form = $this->createForm(new BetType(), $bet);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$bet = $form->getData();
			$currentBalance = $this->getUser()->getBalance();
			if ($currentBalance - $bet->getStake() < 0) {
				echo 'you dont have this much to bet';
				die();
			}

			$this->getUser()->setBalance($currentBalance - $bet->getStake());
			$em->persist($bet);
			$em->persist($this->getUser());
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', 'Your bet has been placed');
			return $this->redirect($this->generateUrl('race_list'));
		}

		return array(
			'entry' => $entry,
			'form' => $form->createView(),
		);
	}

	/**
	 * @Route("/collect/{betID}", name="bet_collect")
	 */
	public function collectAction($betID) {
		$em = $this->getDoctrine()->getManager();

		$bet = $em->getRepository('JPRaceBundle:Bet')->find($betID);

		if (!$bet) {
			throw $this->createNotFoundException('Bet not found');
		}

		//check that the bet belongs to the user
		if ($bet->getUser() !== $this->getUser()) {
			echo 'this bet is not yours to collect';
			die();
		}

		$odds = explode('/', $bet->getOdds());
		$winnings = $bet->getStake() + ($odds[0] * ($bet->getStake() / $odds[1]));
		$currentBalance = $this->getUser()->getBalance();
		$this->getUser()->setBalance($currentBalance + $winnings);

		//now, delete the bet
		$em->remove($bet);
		$em->persist($this->getUser());
		$em->flush();

		$this->get('session')->getFlashBag()->add('success', 'You have received &curr;' . $winnings);
		return $this->redirect($this->generateUrl('race_list'));
	}

}
