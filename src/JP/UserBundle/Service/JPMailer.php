<?php

namespace JP\JobBoardBundle\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class JPMailer {

	protected $mailer;
	protected $router;
	protected $twig;
	protected $parameters;

	public function __construct(\Swift_Mailer $mailer, UrlGeneratorInterface $router, \Twig_Environment $twig, array $parameters) {
		$this->mailer = $mailer;
		$this->router = $router;
		$this->twig = $twig;
		$this->parameters = $parameters;
	}

	public function sendMessage($templateName, $context, $toEmail, $fromEmail = null) {

		$context = $this->twig->mergeGlobals($context);
		$template = $this->twig->loadTemplate($templateName);
		$subject = $template->renderBlock('subject', $context);
		$textBody = $template->renderBlock('body_text', $context);
		$htmlBody = $template->renderBlock('body_html', $context);

		$fromEmail = ($fromEmail !== null) ? $fromEmail : $this->parameters['from_address'];
		$fromName = $this->parameters['from_name'];

		$message = \Swift_Message::newInstance()
			->setSubject($subject)
			->setFrom(array($fromEmail => $fromName))
			->setTo($toEmail);

		if (!empty($htmlBody)) {
			$message->setBody($htmlBody, 'text/html')->addPart($textBody, 'text/plain');
		}
		else {
			$message->setBody($textBody);
		}

		$this->mailer->send($message);
	}
}