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
namespace Teknoo\East\CodeRunnerBundle\Entity\Task;

use Teknoo\East\CodeRunnerBundle\Entity\Task\States\Executed;
use Teknoo\East\CodeRunnerBundle\Entity\Task\States\Registered;
use Teknoo\East\CodeRunnerBundle\Entity\Task\States\Unregistered;
use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\CodeInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\StatusInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;
use Teknoo\States\LifeCycle\StatedClass\Automated\Assertion\Assertion;
use Teknoo\States\LifeCycle\StatedClass\Automated\Assertion\Property\IsInstanceOf;
use Teknoo\States\LifeCycle\StatedClass\Automated\Assertion\Property\IsNotInstanceOf;
use Teknoo\States\LifeCycle\StatedClass\Automated\Assertion\Property\IsNotNull;
use Teknoo\States\LifeCycle\StatedClass\Automated\Assertion\Property\IsNull;
use Teknoo\States\LifeCycle\StatedClass\Automated\AutomatedInterface;
use Teknoo\States\LifeCycle\StatedClass\Automated\AutomatedTrait;
use Teknoo\States\Proxy\IntegratedInterface;
use Teknoo\Bundle\StatesBundle\Entity\IntegratedTrait;
use Teknoo\States\Proxy\ProxyInterface;

/**
 * Class Task
 * @method Task doRegisterStatus(StatusInterface $status)
 * @method Task doRegisterResult(ResultInterface $result)
 * @method Task doSetCode(CodeInterface $code)
 * @method Task doRegisterUrl(string $taskUrl)
 */
class Task implements ProxyInterface, IntegratedInterface, TaskInterface, AutomatedInterface
{
    use IntegratedTrait,
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
    private $id;

    /**
     * @var CodeInterface|\JsonSerializable
     */
    private $code;

    /**
     * @var string
     */
    private $url;

    /**
     * @var StatusInterface|\JsonSerializable
     */
    private $status;

    /**
     * @var ResultInterface|\JsonSerializable
     */
    private $result;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var \DateTime
     */
    private $deletedAt;

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
        //Initialize tests
        $this->updateStates();
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(CodeInterface $code): TaskInterface
    {
        return $this->doSetCode($code);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode(): CodeInterface
    {
        if (!$this->code instanceof CodeInterface) {
            throw new \UnexpectedValueException('Code is not available');
        }

        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl(): string
    {
        if (empty($this->url)) {
            throw new \UnexpectedValueException('Url is not available');
        }

        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus(): StatusInterface
    {
        if (!$this->status instanceof StatusInterface) {
            throw new \UnexpectedValueException('Result is not available');
        }

        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function getResult(): ResultInterface
    {
        if (!$this->result instanceof ResultInterface) {
            throw new \UnexpectedValueException('Result is not available');
        }

        return $this->result;
    }

    /**
     * {@inheritdoc}
     */
    public function registerUrl(string $taskUrl): TaskInterface
    {
        return $this->doRegisterUrl($taskUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function registerStatus(StatusInterface $status): TaskInterface
    {
        return $this->doRegisterStatus($status);
    }

    /**
     * {@inheritdoc}
     */
    public function registerResult(TaskManagerInterface $taskManager, ResultInterface $result): TaskInterface
    {
        return $this->doRegisterResult($result);
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return Task
     */
    public function setCreatedAt(\DateTime $createdAt): Task
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return Task
     */
    public function setUpdatedAt(\DateTime $updatedAt): Task
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime|null $deletedAt
     *
     * @return Task
     */
    public function setDeletedAt(\DateTime $deletedAt=null): Task
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Method to decode a json value to php object instance after doctrine load
     *
     * @param array|\JsonSerializable $value
     * @return null|\JsonSerializable
     */
    private static function decodeJson(&$value)
    {
        if ($value instanceof \JsonSerializable) {
            return $value;
        }

        if (!\is_array($value) || empty($value['class'])) {
            return null;
        }

        if (!\class_exists($value['class'])) {
            return null;
        }

        $class = $value['class'];
        if (is_callable([$class, 'jsonDeserialize'])) {
            return null;
        }

        return $class::jsonDeserialize($value);
    }

    /**
     * @return Task
     */
    public function postLoadJsonUpdate(): Task
    {
        $this->code = static::decodeJson($this->code);
        $this->status = static::decodeJson($this->status);
        $this->result = static::decodeJson($this->result);

        //Initialize states
        $this->updateStates();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatesAssertions(): array
    {
        return [
            (new Assertion([Unregistered::class]))
                ->with('url', new IsNull())
            ,
            (new Assertion([Unregistered::class]))
                ->with('code', new IsNotInstanceOf(CodeInterface::class))
            ,
            (new Assertion([Registered::class]))
                ->with('url', new IsNotNull())
                ->with('code', new IsInstanceOf(CodeInterface::class))
                ->with('result', new IsNotInstanceOf(ResultInterface::class))
            ,
            (new Assertion([Executed::class]))
                ->with('url', new IsNotNull())
                ->with('code', new IsInstanceOf(CodeInterface::class))
                ->with('result', new IsInstanceOf(ResultInterface::class))
            ,
        ];
    }
}