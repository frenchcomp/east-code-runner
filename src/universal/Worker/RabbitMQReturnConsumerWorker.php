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

namespace Teknoo\East\CodeRunner\Worker;

use Doctrine\DBAL\DBALException;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksRegistryInterface;
use Teknoo\East\CodeRunner\Runner\RemotePHP7Runner\RemotePHP7Runner;
use Teknoo\East\CodeRunner\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\StatusInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\East\CodeRunner\Worker\Interfaces\TaintableInterface;
use Teknoo\East\Foundation\Promise\Promise;

/**
 * Class RabbitMQReturnConsumerWorker.
 * AMQP Consumer service, to listen the queue used by the RemotePHP7Runner's worker to return to this platform status
 * and tasks' results. Results and status use the same chanel, the service dispatches them to good Runner manager's
 * methods. Objects are serialized in JSON format and are automatically deserialized by the service.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class RabbitMQReturnConsumerWorker implements ConsumerInterface, TaintableInterface
{
    /**
     * @var TasksRegistryInterface
     */
    private $tasksRegistry;

    /**
     * @var RemotePHP7Runner
     */
    private $remotePHP7Runner;

    /**
     * @var RunnerManagerInterface
     */
    private $runnerManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $taintedFailure = false;

    /**
     * RabbitMQResultConsumerService constructor.
     *
     * @param TasksRegistryInterface $tasksRegistry
     * @param RemotePHP7Runner       $remotePHP7Runner
     * @param RunnerManagerInterface $runnerManager
     * @param LoggerInterface        $logger
     */
    public function __construct(
        TasksRegistryInterface $tasksRegistry,
        RemotePHP7Runner $remotePHP7Runner,
        RunnerManagerInterface $runnerManager,
        LoggerInterface $logger
    ) {
        $this->tasksRegistry = $tasksRegistry;
        $this->remotePHP7Runner = $remotePHP7Runner;
        $this->runnerManager = $runnerManager;
        $this->logger = $logger;
    }

    /**
     * Method to retrieve the original class from the JSON Serialized object (defined in its attribute class), and if
     * the object implements StatusInterface of ResultInterface, unserialize them.
     *
     * @param array $values
     *
     * @return ResultInterface|StatusInterface
     *
     * @throws \DomainException          if the class is not managed here
     * @throws \InvalidArgumentException when the value not embedded the class
     */
    private function jsonDeserialize(array $values)
    {
        if (!isset($values['class']) || !\class_exists($values['class'])) {
            throw new \InvalidArgumentException('class is not matching with the serialized values');
        }

        if (\is_subclass_of($values['class'], StatusInterface::class)) {
            $statusClass = $values['class'];

            return $statusClass::jsonDeserialize($values);
        }

        if (\is_subclass_of($values['class'], ResultInterface::class)) {
            $resultClass = $values['class'];

            return $resultClass::jsonDeserialize($values);
        }

        throw new \DomainException($values['class'].' is not managed her');
    }

    /**
     * To retrieve and extract, from the AMQP message, the object passed by the woerker.
     *
     * @param AMQPMessage $message
     *
     * @return string,ResultInterface|StatusInterface
     *
     * @throws \DomainException          if the class is not managed here
     * @throws \InvalidArgumentException when the value not embedded the class
     */
    private function extractObject(AMQPMessage $message)
    {
        foreach (\json_decode($message->body, true) as $taskUid => $body) {
            return [$taskUid, $this->jsonDeserialize($body)];
        }

        throw new \RuntimeException('Error, the body object is missing');
    }

    /**
     * To analyse and consume a message from RabbitMQ, sent by the remote runner
     * If the task is not managed by this runner, the consumer return false to requeue the message.
     *
     * @param AMQPMessage $msg
     *
     * @return bool
     */
    public function execute(AMQPMessage $msg)
    {
        if (true === $this->isTainted()) {
            return ConsumerInterface::MSG_REJECT_REQUEUE;
        }

        try {
            list($taskUid, $object) = $this->extractObject($msg);

            $this->tasksRegistry->get(
                $taskUid,
                new Promise(
                    function (TaskInterface $task) use ($object) {
                        if ($object instanceof ResultInterface) {
                            $this->runnerManager->pushResult($this->remotePHP7Runner, $task, $object);
                        }

                        if ($object instanceof StatusInterface) {
                            $this->runnerManager->pushStatus($this->remotePHP7Runner, $task, $object);
                        }
                    },
                    function (\Throwable $e) {
                        throw $e;
                    }
                )
            );
        } catch (DBALException $e) {
            $this->logger->critical($e->getMessage().PHP_EOL.$e->getTraceAsString());

            $this->enableTaintedFlag();

            return ConsumerInterface::MSG_REJECT_REQUEUE;
        } catch (\Throwable $e) {
            $this->logger->critical($e->getMessage().PHP_EOL.$e->getTraceAsString());

            return ConsumerInterface::MSG_REJECT;
        }

        return ConsumerInterface::MSG_ACK;
    }

    /**
     * To check if the consumer is tainted or not.
     *
     * @return bool
     */
    private function isTainted(): bool
    {
        return !empty($this->taintedFailure);
    }

    /**
     * To taint this consumer to stop and exit.
     *
     * @return TaintableInterface
     */
    public function enableTaintedFlag(): TaintableInterface
    {
        $this->taintedFailure = true;

        return $this;
    }

    /**
     * To dispatch the information about tainted consumer.
     *
     * @param callable $callback
     *
     * @return TaintableInterface
     */
    public function tellMeIfYouAreTainted(callable $callback): TaintableInterface
    {
        $callback($this->isTainted());

        return $this;
    }
}
