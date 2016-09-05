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
namespace Teknoo\East\CodeRunnerBundle\Runner\RemoteDockerPHP7Runner;

use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunnerBundle\Runner\Capability;
use Teknoo\East\CodeRunnerBundle\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\CodeInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;
use Teknoo\States\LifeCycle\StatedClass\Automated\AutomatedInterface;
use Teknoo\States\LifeCycle\StatedClass\Automated\AutomatedTrait;
use Teknoo\States\Proxy\IntegratedInterface;
use Teknoo\States\Proxy\IntegratedTrait;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\Proxy\ProxyTrait;

class ClassicPHP7Runner implements ProxyInterface, IntegratedInterface, AutomatedInterface, RunnerInterface
{
    use ProxyTrait,
        IntegratedTrait,
        AutomatedTrait;

    /**
     * Class name of the factory to use in set up to initialize this object in this construction.
     *
     * @var string
     */
    protected static $startupFactoryClassName = '\Teknoo\States\Factory\StandardStartupFactory';

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
     * @var RunnerManagerInterface
     */
    private $currentManager;

    /**
     * @var string[]
     */
    private $forbiddenMethodsList = [];

    /**
     * RemoteDockerPHP7Runner constructor.
     * Initialize States behavior.
     * @param string $identifier
     * @param string $name
     * @param array $capabilities
     */
    public function __construct(string $identifier, string $name, array $capabilities)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->capabilities = $capabilities;

        //Call the method of the trait to initialize local attributes of the proxy
        $this->initializeProxy();
        //Call the startup factory to initialize this proxy
        $this->initializeObjectWithFactory();
    }

    /**
     * @param Capability $capability
     * @return ClassicPHP7Runner
     */
    public function addCapability(Capability $capability): ClassicPHP7Runner
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
    public function canYouExecute(RunnerManagerInterface $manager, TaskInterface $task): RunnerInterface
    {
        return $this->doCanYouExecute($manager, $task);
    }

    public function getStatesAssertions(): array
    {
        // TODO: Implement getStatesAssertions() method.
    }
}