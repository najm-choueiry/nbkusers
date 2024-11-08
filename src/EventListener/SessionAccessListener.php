<?php

// src/EventListener/SessionAccessListener.php
// src/EventListener/SessionAccessListener.php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SessionAccessListener implements EventSubscriberInterface
{
	public function onKernelRequest(RequestEvent $event)
	{
		$request = $event->getRequest();

		// Skip the check if the request is for the login page
		if ($request->getPathInfo() === '/login') {
			return;
		}

		// Check if the user is logged in, and if not, redirect to the login page
		if (!$request->getSession()->get('user')) {
			// Redirect to the login page if the user is not logged in
			$response = new RedirectResponse('/login');
			$event->setResponse($response);
		}
	}

	// This event ensures that cache headers are set correctly for pages that shouldn't be cached
	public function onKernelResponse(ResponseEvent $event)
	{
		$response = $event->getResponse();

		// Set no-store, no-cache, and Expires headers to prevent caching of the page
		$response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
		$response->headers->set('Pragma', 'no-cache');
		$response->headers->set('Expires', '0');
	}

	public static function getSubscribedEvents()
	{
		return [
			KernelEvents::REQUEST => 'onKernelRequest',
			KernelEvents::RESPONSE => 'onKernelResponse',
		];
	}
}
