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
	 * @Template()
	 */
	public function listAction() {
		$em = $this->getDoctrine()->getManager();

		$bets = $em->getRepository('JPRaceBundle:Bet')->findByUser($this->getUser());

		return array(
			'bets' => $bets,
		);
	}

	/**
	 * @Route("/remove/{id}", name="bet_remove")
	 */
	public function removeAction($id) {
		$em = $this->getDoctrine()->getManager();

		$bet = $em->getRepository('JPRaceBundle:Bet')->find($id);

		if (!$bet) {
			throw $this->createNotFoundException('Bet not found');
		}

		//check that the bet belongs to the user
		if ($bet->getUser() !== $this->getUser()) {
			echo 'this bet is not yours to remove';
			die();
		}

		$em->remove($bet);
		$em->flush();

		return $this->redirect($this->generateUrl('bet_list'));
	}

	/**
	 * @Route("/{entryID}", name="bet_place")
	 * @Template()
	 */
	public function placeAction($entryID, Request $request) {
		$currentBalance = $this->getUser()->getBalance();
		if ($currentBalance <= 0) {
			$this->get('session')->getFlashBag()->add('error', 'You have 0cr. You need some to place bets.');
			return $this->redirect($this->generateUrl('fos_user_profile_edit'));
		}

		$em = $this->getDoctrine()->getManager();

		$entry = $em->getRepository('JPRaceBundle:Entry')->find($entryID);

		if (!$entry) {
			throw $this->createNotFoundException('Race entry not found');
		}
		elseif ($entry->getRace()->getComplete()) {
			throw $this->createNotFoundException('The race has already been completed');
		}

		$bet = new Bet();
		$bet->setEntry($entry);
		$bet->setOdds($entry->getOdds());
		$bet->setUser($this->getUser());
		$bet->setRace($entry->getRace());
		$bet->setHorse($entry->getHorse());

		$form = $this->createForm(new BetType(), $bet);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$bet = $form->getData();
			if ($currentBalance - $bet->getStake() < 0) {
				$this->get('session')->getFlashBag()->add('error', 'You only have ' . $currentBalance . 'cr to bet with.');
				return $this->redirect($this->generateUrl('bet_place', array('entryID' => $entryID)));
			}

			$this->getUser()->setBalance($currentBalance - $bet->getStake());
			$em->persist($bet);
			$em->persist($this->getUser());
			$em->flush();

			$this->get('session')->getFlashBag()->add('success', 'Your bet of ' . $bet->getStake() . ' cr has been placed');
			return $this->redirect($this->generateUrl('race_view', array('id' => $entry->getRace()->getId())));
		}

		return array(
			'entry' => $entry,
			'form' => $form->createView(),
		);
	}

	/**
	 * @Route("/collect/{id}", name="bet_collect")
	 */
	public function collectAction($id) {
		$em = $this->getDoctrine()->getManager();

		$bet = $em->getRepository('JPRaceBundle:Bet')->find($id);

		if (!$bet) {
			throw $this->createNotFoundException('Bet not found');
		}

		//check that the bet belongs to the user
		if ($bet->getUser() !== $this->getUser()) {
			throw $this->createAccessDeniedException('Bet '.$id.' does not belong to user '.$this->getUser()->getId());
		}

		$odds = explode('/', $bet->getOdds());
		$winnings = $bet->getStake() + ($odds[0] * ($bet->getStake() / $odds[1]));
		$this->getUser()->addCredits($winnings);

		//now, delete the bet
		$em->remove($bet);
		$em->persist($this->getUser());
		$em->flush();

		$this->get('session')->getFlashBag()->add('success', 'You have received ' . $winnings . 'cr');

		return $this->redirect($this->generateUrl('race_list'));
	}

}
