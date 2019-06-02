<?php

namespace Mshavliuk\SignalEventsBundle\Tests\DependencyInjection;

use Exception;
use Mshavliuk\SignalEventsBundle\Command\SupportedSignalsCommand;
use Mshavliuk\SignalEventsBundle\DependencyInjection\SignalEventsExtension;
use Mshavliuk\SignalEventsBundle\EventListener\ServiceStartupListener;
use Mshavliuk\SignalEventsBundle\Service\SignalConstants;
use Mshavliuk\SignalEventsBundle\Service\SignalHandlerService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\KernelEvents;

class SignalEventsExtensionTest extends TestCase
{
    public function testContainerHasDefinition(): void
    {
        $container = $this->getContainer();
        $this->assertTrue($container->hasDefinition(SignalHandlerService::class));
    }

    public function testContainerHasAlias(): void
    {
        $alias = 'signal_events.handle_service';

        $container = $this->getContainer();
        $container->hasAlias($alias);

        $definitionByAlias = $container->findDefinition($alias);
        $this->assertInstanceOf(Definition::class, $definitionByAlias);

        $this->assertDICDefinitionClass($definitionByAlias, SignalHandlerService::class);
    }

    public function testContainerWillRegisterCommand(): void
    {
        $container = $this->getContainer();
        $this->assertTrue($container->hasDefinition(SupportedSignalsCommand::class));
    }

    /**
     * @dataProvider providerSignals
     *
     * @param array<string> $signals
     */
    public function testSetMethodCallWithSignals($signals): void
    {
        $container = $this->getContainer([['handle_signals' => $signals, 'startup_events' => ['console.event']]]);

        $serviceDefinition = $container->getDefinition(SignalHandlerService::class);
        $this->assertDICDefinitionMethodCallAt(0, $serviceDefinition, 'addObservableSignals', $signals);
        $this->assertTrue($serviceDefinition->isPublic());
    }

    /**
     * @dataProvider providerStartupEvents
     *
     * @param array<string> $events
     */
    public function testListenerWillSetEventTags($events): void
    {
        $container = $this->getContainer([['startup_events' => $events]]);

        $startupListenerDefinition = $serviceDefinition = $container->getDefinition(ServiceStartupListener::class);
        $this->assertTrue($startupListenerDefinition->hasTag('kernel.event_listener'));
        $tags = $startupListenerDefinition->getTag('kernel.event_listener');
        $this->assertCount(count($events), $tags);
    }

    public function testGetAliasFunctionWillReturnString(): void
    {
        $extension = new SignalEventsExtension();
        $this->assertIsString($extension->getAlias());
        $this->assertNotEmpty($extension->getAlias());
    }

    public function providerStartupEvents(): array
    {
        return [
            'console' => [[ConsoleEvents::COMMAND]],
            'request' => [[KernelEvents::REQUEST]],
            'console&request' => [[ConsoleEvents::COMMAND, KernelEvents::REQUEST]],
        ];
    }

    public function providerSignals(): array
    {
        return [
            'SIGINT' => [['SIGINT']],
            'SIGHUP' => [['SIGHUP']],
            'supported signals' => [SignalConstants::SUPPORTED_SIGNALS],
            'two signals' => [['SIGINT', 'SIGHUP']],
        ];
    }

    /**
     * Assertion on the Class of a DIC Service Definition.
     *
     * @param Definition $definition
     * @param string                                            $expectedClass
     */
    protected function assertDICDefinitionClass($definition, $expectedClass): void
    {
        $this->assertEquals($expectedClass, $definition->getClass(), 'Expected Class of the DIC Container Service Definition is wrong.');
    }

    /**
     * @param int $pos
     * @param Definition $definition
     * @param string $methodName
     * @param array|null $params
     */
    protected function assertDICDefinitionMethodCallAt($pos, $definition, $methodName, array $params = null): void
    {
        $calls = $definition->getMethodCalls();
        if (isset($calls[$pos][0])) {
            $this->assertEquals($methodName, $calls[$pos][0], "Method '".$methodName."' is expected to be called at position $pos.");

            if (null !== $params) {
                $this->assertEquals($params, $calls[$pos][1], "Expected parameters to methods '".$methodName."' do not match the actual parameters.");
            }
        } else {
            $this->fail("Method '".$methodName."' is expected to be called at position $pos.");
        }
    }

    /**
     * @param array $config
     *
     * @throws Exception
     *
     * @return ContainerBuilder
     */
    protected function getContainer(array $config = []): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);

        $loader = new SignalEventsExtension();
        $loader->load($config, $container);
        $container->compile();

        return $container;
    }
}
