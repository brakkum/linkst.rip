<?php
namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

class Redirect404ToHomepageListener
{
    /**
    * @var RouterInterface
    */
    private $router;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
    * @var RouterInterface $router
    * @var LoggerInterface $logger
    */
    public function __construct(RouterInterface $router, LoggerInterface $logger)
    {
        $this->router = $router;
        $this->logger = $logger;
    }

    /**
    * @var GetResponseForExceptionEvent $event
    * @return null
    */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // If not a HttpNotFoundException ignore
        if (!$event->getException() instanceof NotFoundHttpException) {
            $this->logger->log(LogLevel::WARNING,"Something happened {$event->getRequest()}");
            return false;
        }

        $request = $event->getRequest();
        $slug = $request->getPathInfo();

        $this->logger->log(LogLevel::ERROR,"Bad slug {$slug}");

        // Create redirect response with url for the home page
        $response = new RedirectResponse($this->router->generate('index'));

        // Set the response to be processed
        return $event->setResponse($response);
    }
}
