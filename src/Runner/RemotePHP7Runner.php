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
namespace Teknoo\East\CodeRunnerBundle\Runner;

use OldSound\RabbitMqBundle\RabbitMq\Consumer;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunnerBundle\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;
use Teknoo\States\LifeCycle\StatedClass\Automated\Assertion\Assertion;
use Teknoo\States\LifeCycle\StatedClass\Automated\Assertion\Property\IsInstanceOf;
use Teknoo\States\LifeCycle\StatedClass\Automated\Assertion\Property\IsNotInstanceOf;

class RemotePHP7Runner implements RunnerInterface
{
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
     * @var ResultInterface
     */
    private $currentResult;

    /**
     * @var Producer
     */
    private $taskProducer;

    /**
     * @var Consumer
     */
    private $resultConsumer;

    /**
     * RemotePHP7Runner constructor.
     * Initialize States behavior.
     * @param Producer $taskProducer
     * @param Consumer $resultConsumer
     * @param string $identifier
     * @param string $name
     * @param string $version
     * @param array $capabilities
     */
    public function __construct(
        Producer $taskProducer,
        Consumer $resultConsumer,
        string $identifier,
        string $name,
        string $version,
        array $capabilities
    ) {
        $this->taskProducer = $taskProducer;
        $this->resultConsumer = $resultConsumer;
        $this->identifier = $identifier;
        $this->name = $name;
        $this->version = $version;
        $this->capabilities = $capabilities;
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
        // TODO: Implement reset() method.
    }

    /**
     * {@inheritdoc}
     */
    public function canYouExecute(RunnerManagerInterface $manager, TaskInterface $task): RunnerInterface
    {
        // TODO: Implement canYouExecute() method.
    }
}