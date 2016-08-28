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

use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\CodeInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\StatusInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;
use Teknoo\States\Proxy\IntegratedInterface;
use Teknoo\Bundle\StatesBundle\Entity\IntegratedTrait;
use Teknoo\States\Proxy\ProxyInterface;
use Teknoo\States\Proxy\ProxyTrait;

/**
 * Class Task
 */
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
    public function registerUrl(string $taskUrl): TaskInterface
    {
        $this->url = $taskUrl;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function registerStatus(StatusInterface $status): TaskInterface
    {
        $this->status = $status;

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
     * @param mixed $deletedAt
     *
     * @return Task
     */
    public function setDeletedAt($deletedAt): Task
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

        return $this;
    }
}