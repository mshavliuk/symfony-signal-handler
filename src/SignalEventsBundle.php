<?php


namespace Mshavliuk\SignalEventsBundle;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SignalEventsBundle extends Bundle
{
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

}