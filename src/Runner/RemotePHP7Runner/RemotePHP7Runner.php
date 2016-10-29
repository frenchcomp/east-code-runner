<?php

/**
 * East CodeRunnerBundle.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east/coderunner Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace Teknoo\East\CodeRunnerBundle\Runner\RemotePHP7Runner;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunnerBundle\Runner\Capability;
use Teknoo\East\CodeRunnerBundle\Runner\CheckRequirementsTrait;
use Teknoo\East\CodeRunnerBundle\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunnerBundle\Runner\RemotePHP7Runner\States\Awaiting;
use Teknoo\East\CodeRunnerBundle\Runner\RemotePHP7Runner\States\Busy;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;
use Teknoo\States\LifeCycle\StatedClass\Automated\Assertion\Assertion;
use Teknoo\States\LifeCycle\StatedClass\Automated\Assertion\Property\IsInstanceOf;
use Teknoo\States\LifeCycle\StatedClass\Automated\Assertion\Property\IsNotInstanceOf;
use Teknoo\States\LifeCycle\StatedClass\Automated\AutomatedInterface;
use Teknoo\States\LifeCycle\StatedClass\Automated\AutomatedTrait;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\Proxy\ProxyTrait;

class RemotePHP7Runner implements ProxyInterface, AutomatedInterface, RunnerInterface
{
    use ProxyTrait,
        AutomatedTrait,
        CheckRequirementsTrait;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $version;

    /**
     * @var Capability[]
     */
    private $capabilities;

    /**
     * @var TaskInterface
     */
    private $currentTask;

    /**
     * @var ProducerInterface
     */
    private $taskProducer;

    /**
     * RemotePHP7Runner constructor.
     * Initialize States behavior.
     * @param ProducerInterface $taskProducer
     * @param string $identifier
     * @param string $name
     * @param string $version
     * @param array $capabilities
     */
    public function __construct(
        ProducerInterface $taskProducer,
        string $identifier,
        string $name,
        string $version,
        array $capabilities
    ) {
        $this->taskProducer = $taskProducer;
        $this->identifier = $identifier;
        $this->name = $name;
        $this->version = $version;
        $this->capabilities = $capabilities;

        //Call the method of the trait to initialize local attributes of the proxy
        $this->initializeProxy();
        $this->updateStates();
    }

    /**
     * {@inheritdoc}
     */
    public static function statesListDeclaration(): array
    {
        return [
            Awaiting::class,
            Busy::class
        ];
    }

    /**
     * @param Capability $capability
     * @return RemotePHP7Runner
     */
    public function addCapability(Capability $capability): RemotePHP7Runner
    {
        $this->capabilities[] = $capability;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function getCapabilities(): array
    {
        return $this->capabilities;
    }

    /**
     * {@inheritdoc}
     */
    public function reset(): RunnerInterface
    {
        return $this->doReset();
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RunnerManagerInterface $manager, TaskInterface $task): RunnerInterface
    {
        return $this->doExecute($manager, $task);
    }

    /**
     * {@inheritdoc}
     */
    public function rememberYourCurrentTask(TaskInterface $task): RunnerInterface
    {
        $this->currentTask = $task;
        $this->updateStates();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatesAssertions(): array
    {
        return [
            (new Assertion(Awaiting::class))->with('currentTask', new IsNotInstanceOf(TaskInterface::class)),
            (new Assertion(Busy::class))->with('currentTask', new IsInstanceOf(TaskInterface::class)),
        ];
    }
}