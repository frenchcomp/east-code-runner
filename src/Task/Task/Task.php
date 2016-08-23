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
namespace Teknoo\East\CodeRunnerBundle\Task\Task;

use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\CodeInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\StatusInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;
use Teknoo\States\Proxy\IntegratedInterface;
use Teknoo\States\Proxy\IntegratedTrait;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\Proxy\ProxyTrait;

class Task implements ProxyInterface, IntegratedInterface, TaskInterface
{
    use ProxyTrait,
        IntegratedTrait;

    /**
     * Class name of the factory to use in set up to initialize this object in this construction.
     *
     * @var string
     */
    protected static $startupFactoryClassName = '\Teknoo\States\Factory\StandardStartupFactory';

    /**
     * @var CodeInterface
     */
    private $code;

    /**
     * @var string
     */
    private $url;

    /**
     * @var StatusInterface
     */
    private $status;

    /**
     * @var ResultInterface
     */
    private $result;

    /**
     * Manager constructor.
     * Initialize States behavior.
     */
    public function __construct()
    {
        //Call the method of the trait to initialize local attributes of the proxy
        $this->initializeProxy();
        //Call the startup factory to initialize this proxy
        $this->initializeObjectWithFactory();
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(CodeInterface $code): TaskInterface
    {
        $this->code = $code;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode(): CodeInterface
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus(): StatusInterface
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function getResult(): ResultInterface
    {
        return $this->result;
    }

    /**
     * {@inheritdoc}
     */
    public function registerTaskManagerExecuting(string $taskUrl, TaskManagerInterface $taskManager): TaskInterface
    {
        $this->url = $taskUrl;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function registerResult(TaskManagerInterface $taskManager, ResultInterface $result): TaskInterface
    {
        $this->result = $result;

        return $this;
    }
}