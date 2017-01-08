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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east/coderunner Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\East\CodeRunner\Entity\Task;

use Teknoo\East\CodeRunner\Entity\Task\States\Executed;
use Teknoo\East\CodeRunner\Entity\Task\States\Registered;
use Teknoo\East\CodeRunner\Entity\Task\States\Unregistered;
use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\CodeInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\StatusInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\States\LifeCycle\StatedClass\Automated\Assertion\Assertion;
use Teknoo\States\LifeCycle\StatedClass\Automated\Assertion\Property\IsInstanceOf;
use Teknoo\States\LifeCycle\StatedClass\Automated\Assertion\Property\IsNotInstanceOf;
use Teknoo\States\LifeCycle\StatedClass\Automated\Assertion\Property\IsNotNull;
use Teknoo\States\LifeCycle\StatedClass\Automated\Assertion\Property\IsNull;
use Teknoo\States\LifeCycle\StatedClass\Automated\AutomatedInterface;
use Teknoo\States\LifeCycle\StatedClass\Automated\AutomatedTrait;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\Proxy\ProxyTrait;

/**
 * Class Task.
 *
 * @method Task doRegisterStatus(StatusInterface $status)
 * @method Task doRegisterResult(ResultInterface $result)
 * @method Task doSetCode(CodeInterface $code)
 * @method Task doRegisterUrl(string $taskUrl)
 */
class Task implements ProxyInterface, TaskInterface, AutomatedInterface
{
    use ProxyTrait,
        AutomatedTrait;

    /**
     * @var string
     */
    private $id;

    /**
     * @var CodeInterface|\JsonSerializable
     */
    private $codeInstance;

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
    private $statusInstance;

    /**
     * @var StatusInterface|\JsonSerializable
     */
    private $status;

    /**
     * @var ResultInterface|\JsonSerializable
     */
    private $resultInstance;

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
        //Initialize tests
        $this->updateStates();
    }

    /**
     * {@inheritdoc}
     */
    public static function statesListDeclaration(): array
    {
        return [
            Executed::class,
            Registered::class,
            Unregistered::class,
        ];
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
        if (!$this->codeInstance instanceof CodeInterface) {
            throw new \UnexpectedValueException('Code is not available');
        }

        return $this->codeInstance;
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
        if (!$this->statusInstance instanceof StatusInterface) {
            throw new \UnexpectedValueException('Result is not available');
        }

        return $this->statusInstance;
    }

    /**
     * {@inheritdoc}
     */
    public function getResult(): ResultInterface
    {
        if (!$this->resultInstance instanceof ResultInterface) {
            throw new \UnexpectedValueException('Result is not available');
        }

        return $this->resultInstance;
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
    public function setDeletedAt(\DateTime $deletedAt = null): Task
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Method to decode a json value to php object instance after doctrine load.
     *
     * @param array|\JsonSerializable $value
     *
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
        if (!is_callable([$class, 'jsonDeserialize'])) {
            return null;
        }

        return $class::jsonDeserialize($value);
    }

    /**
     * @return Task
     */
    public function postLoadJsonUpdate(): Task
    {
        //Call the method of the trait to initialize local attributes of the proxy
        $this->initializeProxy();
        //Initialize tests
        $this->updateStates();

        $this->codeInstance = static::decodeJson($this->code);
        $this->statusInstance = static::decodeJson($this->status);
        $this->resultInstance = static::decodeJson($this->result);

        //Initialize states
        $this->updateStates();

        return $this;
    }

    /**
     * @return Task
     */
    public function prePersistJsonUpdate(): Task
    {
        $this->code = \json_encode($this->codeInstance);
        $this->status = \json_encode($this->statusInstance);
        $this->result = \json_encode($this->resultInstance);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatesAssertions(): array
    {
        return [
            (new Assertion([Unregistered::class]))
                ->with('url', new IsNull()),
            (new Assertion([Unregistered::class]))
                ->with('codeInstance', new IsNotInstanceOf(CodeInterface::class)),
            (new Assertion([Registered::class]))
                ->with('url', new IsNotNull())
                ->with('codeInstance', new IsInstanceOf(CodeInterface::class))
                ->with('resultInstance', new IsNotInstanceOf(ResultInterface::class)),
            (new Assertion([Executed::class]))
                ->with('url', new IsNotNull())
                ->with('codeInstance', new IsInstanceOf(CodeInterface::class))
                ->with('resultInstance', new IsInstanceOf(ResultInterface::class)),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        $deletedAt = null;
        if ($this->deletedAt instanceof \DateTime) {
            $deletedAt = $this->deletedAt->format('Y-m-d H:i:s');
        }

        $createdAt = null;
        if ($this->createdAt instanceof \DateTime) {
            $createdAt = $this->createdAt->format('Y-m-d H:i:s');
        }

        $updatedAt = null;
        if ($this->updatedAt instanceof \DateTime) {
            $updatedAt = $this->updatedAt->format('Y-m-d H:i:s');
        }

        return [
            'class' => static::class,
            'id' => $this->id,
            'code' => $this->codeInstance,
            'url' => $this->url,
            'status' => $this->statusInstance,
            'result' => $this->resultInstance,
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
            'deletedAt' => $deletedAt,
        ];
    }

    /**
     * To restore states after doctrine load
     */
    public function __wakeup()
    {
        //Call the method of the trait to initialize local attributes of the proxy
        $this->initializeProxy();
        //Initialize tests
        $this->updateStates();
    }

    /**
     * {@inheritdoc}
     */
    public static function jsonDeserialize(array $values): TaskInterface
    {
        if (!isset($values['class']) || static::class != $values['class']) {
            throw new \InvalidArgumentException('class is not matching with the serialized values');
        }

        $code = $values['code'];
        if (isset($code['class'])
            && \is_subclass_of($code['class'], CodeInterface::class)) {
            $codeClass = $code['class'];
            $code = $codeClass::jsonDeserialize($code);
        }

        $status = $values['status'];
        if (isset($status['class'])
            && \is_subclass_of($status['class'], StatusInterface::class)) {
            $statusClass = $status['class'];
            $status = $statusClass::jsonDeserialize($status);
        }

        $result = $values['result'];
        if (isset($result['class'])
            && \is_subclass_of($result['class'], ResultInterface::class)) {
            $resultClass = $result['class'];
            $result = $resultClass::jsonDeserialize($result);
        }

        $deletedAt = null;
        if (!empty($values['deletedAt'])) {
            $deletedAt = new \DateTime($values['deletedAt']);
        }

        $createdAt = null;
        if (!empty($values['createdAt'])) {
            $createdAt = new \DateTime($values['createdAt']);
        }

        $updatedAt = null;
        if (!empty($values['updatedAt'])) {
            $updatedAt = new \DateTime($values['updatedAt']);
        }

        $task = new static();
        $task->id = $values['id'];
        $task->codeInstance = $code;
        $task->url = $values['url'];
        $task->statusInstance = $status;
        $task->resultInstance = $result;
        $task->createdAt = $createdAt;
        $task->updatedAt = $updatedAt;
        $task->deletedAt = $deletedAt;

        $task->updateStates();

        return $task;
    }
}
