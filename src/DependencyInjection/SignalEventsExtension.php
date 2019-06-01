<?php

namespace Mshavliuk\SignalEventsBundle\DependencyInjection;

use Exception;
use Mshavliuk\SignalEventsBundle\EventListener\ServiceStartupListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SignalEventsExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);


        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.yaml');

        $serviceDefinition = $container->findDefinition('signal_events.handle_service');
        if ($config['startup_events']) {
            $this->defineEventListener($container, $config['startup_events']);
            $serviceDefinition->addMethodCall('addObservableSignals', $config['handle_signals']);
        }
    }

    protected function defineEventListener(ContainerBuilder $container, $events): Definition
    {
        $definition = new Definition(ServiceStartupListener::class);
        $definition->setAutowired(true);
        foreach ($events as $event) {
            $definition->addTag('kernel.event_listener', ['event' => $event, 'method' => 'handleStartupEvent']);
        }
        $container->setDefinition(ServiceStartupListener::class, $definition);

        return $definition;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'signal_events';
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration();
    }
}
