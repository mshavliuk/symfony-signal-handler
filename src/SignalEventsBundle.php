<?php


namespace Mshavliuk\SignalEventsBundle;


use Mshavliuk\SignalEventsBundle\DependencyInjection\SignalEventsExtension;
use Mshavliuk\SignalEventsBundle\Service\SignalHandlerService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SignalEventsBundle extends Bundle
{
    /**
     * @var SignalHandlerService
     */
    private $signalHandlerService;

    /**
     * @inheritDoc
     */
    public function shutdown()
    {
        parent::shutdown(); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritDoc
     */
    public function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
        $this->signalHandlerService = new SignalHandlerService();
    }

    /**
     * @inheritDoc
     */
    protected function createContainerExtension()
    {
        return new SignalEventsExtension();
    }

}