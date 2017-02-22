<?php

/**
 * East CodeRunner.
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

namespace Teknoo\Tests\East\CodeRunner\Service;

use Doctrine\DBAL\DBALException;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Teknoo\East\CodeRunner\Entity\Task\Task;
use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksRegistryInterface;
use Teknoo\East\CodeRunner\Runner\RemotePHP7Runner\RemotePHP7Runner;
use Teknoo\East\CodeRunner\Service\RabbitMQReturnConsumerService;
use Teknoo\East\CodeRunner\Task\Status;
use Teknoo\East\CodeRunner\Task\TextResult;
use Teknoo\East\Foundation\Promise\PromiseInterface;

/**
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers \Teknoo\East\CodeRunner\Service\RabbitMQReturnConsumerService
 */
class RabbitMQReturnConsumerServiceTest extends \PHPUnit_Framework_TestCase
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
     * @return \PHPUnit_Framework_MockObject_MockObject|TasksRegistryInterface
     */
    public function getTasksRegistry(): TasksRegistryInterface
    {
        if (!$this->tasksRegistry instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->tasksRegistry = $this->createMock(TasksRegistryInterface::class);
        }

        return $this->tasksRegistry;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RemotePHP7Runner
     */
    public function getRemotePHP7Runner()
    {
        if (!$this->remotePHP7Runner instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->remotePHP7Runner = $this->createMock(RemotePHP7Runner::class);
        }

        return $this->remotePHP7Runner;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RunnerManagerInterface
     */
    public function getRunnerManager()
    {
        if (!$this->runnerManager instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->runnerManager = $this->createMock(RunnerManagerInterface::class);
        }

        return $this->runnerManager;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    public function getLogger()
    {
        if (!$this->logger instanceof \PHPUnit_Framework_MockObject_MockObject) {
            $this->logger = $this->createMock(LoggerInterface::class);
        }

        return $this->logger;
    }

    /**
     * @return RabbitMQReturnConsumerService
     */
    public function buildService()
    {
        return new RabbitMQReturnConsumerService(
            $this->getTasksRegistry(),
            $this->getRemotePHP7Runner(),
            $this->getRunnerManager(),
            $this->getLogger()
        );
    }

    public function testExecuteBadMessage()
    {
        $message = new AMQPMessage();

        $this->getLogger()->expects(self::once())->method('critical');

        self::assertEquals(ConsumerInterface::MSG_REJECT, $this->buildService()->execute($message));
    }

    public function testExecuteBadBehaviorOfManager()
    {
        $result = new TextResult('foo', 'bar', '7.1', 123, 345);
        $message = new AMQPMessage();
        $task = new Task();
        $message->body = json_encode(['https://foo.bar'=>$result]);

        $this->getTasksRegistry()
            ->expects(self::once())
            ->method('get')
            ->willReturnCallback(function ($taskUrl, PromiseInterface $promise) use ($task) {
                self::assertEquals('https://foo.bar', $taskUrl);
                $promise->success($task);

                return $this->getTasksRegistry();
            });

        $this->getRunnerManager()
            ->expects(self::once())
            ->method('pushResult')
            ->with($this->getRemotePHP7Runner(), $task, $result)
            ->willThrowException(new \Exception());

        $this->getLogger()->expects(self::once())->method('critical');

        self::assertEquals(ConsumerInterface::MSG_REJECT, $this->buildService()->execute($message));
    }

    public function testExecuteTaskMissingInMessage()
    {
        $message = new AMQPMessage();
        $message->body = json_encode([]);

        $this->getLogger()->expects(self::once())->method('critical');

        self::assertEquals(ConsumerInterface::MSG_REJECT, $this->buildService()->execute($message));
    }

    public function testExecuteTaskMissingInRegistry()
    {
        $result = new TextResult('foo', 'bar', '7.1', 123, 345);
        $message = new AMQPMessage();
        $message->body = json_encode(['https://foo.bar'=>$result]);

        $this->getTasksRegistry()
            ->expects(self::once())
            ->method('get')
            ->willReturnCallback(function ($taskUrl, PromiseInterface $promise) {
                self::assertEquals('https://foo.bar', $taskUrl);
                $promise->fail(new \DomainException());

                return $this->getTasksRegistry();
            });

        $this->getLogger()->expects(self::once())->method('critical');

        self::assertEquals(ConsumerInterface::MSG_REJECT, $this->buildService()->execute($message));
    }

    public function testExecuteBadMessageClass()
    {
        $message = new AMQPMessage();
        $message->body = json_encode(['https://foo.bar'=>['foo' => 'bar']]);

        $this->getLogger()->expects(self::once())->method('critical');

        self::assertEquals(ConsumerInterface::MSG_REJECT, $this->buildService()->execute($message));
    }

    public function testExecuteBadMessageNotManager()
    {
        $message = new AMQPMessage();
        $message->body = json_encode(['https://foo.bar'=>new Task()]);

        $this->getLogger()->expects(self::once())->method('critical');

        self::assertEquals(ConsumerInterface::MSG_REJECT, $this->buildService()->execute($message));
    }

    public function testExecuteResult()
    {
        $result = new TextResult('foo', 'bar', '7.1', 123, 345);
        $message = new AMQPMessage();
        $task = new Task();
        $message->body = json_encode(['https://foo.bar'=>$result]);

        $this->getTasksRegistry()
            ->expects(self::once())
            ->method('get')
            ->willReturnCallback(function ($taskUrl, PromiseInterface $promise) use ($task) {
                self::assertEquals('https://foo.bar', $taskUrl);
                $promise->success($task);

                return $this->getTasksRegistry();
            });

        $this->getRunnerManager()
            ->expects(self::once())
            ->method('pushResult')
            ->with($this->getRemotePHP7Runner(), $task, $result)
            ->willReturnSelf();

        $this->getLogger()->expects(self::never())->method('critical');

        self::assertEquals(ConsumerInterface::MSG_ACK, $this->buildService()->execute($message));
    }

    public function testExecuteStatus()
    {
        $status = new Status('foo');
        $message = new AMQPMessage();
        $task = new Task();
        $message->body = json_encode(['https://foo.bar'=>$status]);

        $this->getTasksRegistry()
            ->expects(self::once())
            ->method('get')
            ->willReturnCallback(function ($taskUrl, PromiseInterface $promise) use ($task) {
                self::assertEquals('https://foo.bar', $taskUrl);
                $promise->success($task);

                return $this->getTasksRegistry();
            });

        $this->getRunnerManager()
            ->expects(self::once())
            ->method('pushStatus')
            ->with($this->getRemotePHP7Runner(), $task, $status)
            ->willReturnSelf();

        $this->getLogger()->expects(self::never())->method('critical');

        self::assertEquals(ConsumerInterface::MSG_ACK, $this->buildService()->execute($message));
    }

    public function testExecuteStatusErrorDBal()
    {
        $status = new Status('foo');
        $message = new AMQPMessage();
        $task = new Task();
        $message->body = json_encode(['https://foo.bar'=>$status]);

        $this->getTasksRegistry()
            ->expects(self::once())
            ->method('get')
            ->willReturnCallback(function ($taskUrl, PromiseInterface $promise) use ($task) {
                self::assertEquals('https://foo.bar', $taskUrl);
                $promise->success($task);

                return $this->getTasksRegistry();
            });

        $this->getRunnerManager()
            ->expects(self::once())
            ->method('pushStatus')
            ->with($this->getRemotePHP7Runner(), $task, $status)
            ->willThrowException(new DBALException());

        $this->getLogger()->expects(self::once())->method('critical');

        self::assertEquals(ConsumerInterface::MSG_REJECT_REQUEUE, $this->buildService()->execute($message));
    }
}
