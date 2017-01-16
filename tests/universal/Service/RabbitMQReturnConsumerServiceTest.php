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

use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Teknoo\East\CodeRunner\Entity\Task\Task;
use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Runner\RemotePHP7Runner\RemotePHP7Runner;
use Teknoo\East\CodeRunner\Service\RabbitMQReturnConsumerService;
use Teknoo\East\CodeRunner\Task\Status;
use Teknoo\East\CodeRunner\Task\TextResult;

/**
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers \Teknoo\East\CodeRunner\Service\RabbitMQReturnConsumerService
 */
class RabbitMQReturnConsumerServiceTest extends \PHPUnit_Framework_TestCase
{
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
            $this->getRemotePHP7Runner(),
            $this->getRunnerManager(),
            $this->getLogger()
        );
    }

    public function testExecuteBadMessage()
    {
        $message = new AMQPMessage();

        $this->getLogger()->expects(self::once())->method('critical');

        self::assertTrue($this->buildService()->execute($message));
    }

    public function testExecuteBadBehaviorOfManager()
    {
        $result = new TextResult('foo', 'bar', '7.1', 123, 345);
        $message = new AMQPMessage();
        $message->body = json_encode($result);

        $this->getRunnerManager()
            ->expects(self::once())
            ->method('pushResult')
            ->with($this->getRemotePHP7Runner(), $result)
            ->willThrowException(new \Exception());

        $this->getLogger()->expects(self::once())->method('critical');

        self::assertTrue($this->buildService()->execute($message));
    }

    public function testExecuteBadMessageClass()
    {
        $message = new AMQPMessage();
        $message->body = json_encode(['foo'=>'bar']);

        $this->getLogger()->expects(self::once())->method('critical');

        self::assertTrue($this->buildService()->execute($message));
    }

    public function testExecuteBadMessageNotManager()
    {
        $message = new AMQPMessage();
        $message->body = json_encode(new Task());

        $this->getLogger()->expects(self::once())->method('critical');

        self::assertTrue($this->buildService()->execute($message));
    }

    public function testExecuteResult()
    {
        $result = new TextResult('foo', 'bar', '7.1', 123, 345);
        $message = new AMQPMessage();
        $message->body = json_encode($result);

        $this->getRunnerManager()
            ->expects(self::once())
            ->method('pushResult')
            ->with($this->getRemotePHP7Runner(), $result)
            ->willReturnSelf();

        $this->getLogger()->expects(self::never())->method('critical');

        self::assertTrue($this->buildService()->execute($message));
    }

    public function testExecuteStatus()
    {
        $status = new Status('foo');
        $message = new AMQPMessage();
        $message->body = json_encode($status);

        $this->getRunnerManager()
            ->expects(self::once())
            ->method('pushStatus')
            ->with($this->getRemotePHP7Runner(), $status)
            ->willReturnSelf();

        $this->getLogger()->expects(self::never())->method('critical');

        self::assertTrue($this->buildService()->execute($message));
    }
}
