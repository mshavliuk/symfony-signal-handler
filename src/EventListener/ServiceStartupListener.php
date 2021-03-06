<?php

declare(strict_types=1);

namespace Mshavliuk\MshavliukSignalEventsBundle\EventListener;

use Exception;
use Mshavliuk\MshavliukSignalEventsBundle\Service\SignalHandlerService;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;

class ServiceStartupListener
{
    /** @var ContainerInterface */
    protected $container;
    /** @var bool */
    protected $initialized = false;

    /**
     * ServiceStartupListener constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Event|ConsoleCommandEvent $event
     *
     * @throws Exception
     */
    public function handleStartupEvent($event): void
    {
        if (!$this->initialized) {
            $this->container->get(SignalHandlerService::class);
            $this->initialized = true;
        }
    }
}
