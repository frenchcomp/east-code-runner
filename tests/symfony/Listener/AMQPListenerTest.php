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

namespace Teknoo\Tests\East\CodeRunnerBundle\DependencyInjection;

use OldSound\RabbitMqBundle\Event\AfterProcessingMessageEvent;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Teknoo\East\CodeRunner\Worker\Interfaces\TaintableInterface;
use Teknoo\East\CodeRunnerBundle\Listener\AMQPListener;

/**
 * Class AMQPListenerTest.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers \Teknoo\East\CodeRunnerBundle\Listener\AMQPListener
 */
class AMQPListenerTest extends \PHPUnit_Framework_TestCase
{
    public function buildService(callable $callable): AMQPListener
    {
        return new AMQPListener($callable);
    }

    public function testOnAfterProcessingMessageNotTaintableWorker()
    {
        $callable = function () {
            self::fail('This method must be not called');
        };

        $event = $this->createMock(AfterProcessingMessageEvent::class);

        self::assertInstanceOf(
            AMQPListener::class,
            $this->buildService($callable)->onAfterProcessingMessage($event)
        );
    }

    public function testOnAfterProcessingMessageTaintableWorkerNotTainted()
    {
        $callable = function () use (&$called) {
            self::fail('This method must be not called');
        };

        $consumer = new class implements ConsumerInterface, TaintableInterface
        {
            public function execute(AMQPMessage $msg)
            {
                // TODO: Implement execute() method.
            }

            public function enableTaintedFlag(): TaintableInterface
            {
                return $this;
            }

            public function tellMeIfYouAreTainted(callable $callback): TaintableInterface
            {
                $callback(false);
                return $this;
            }
        };

        $event = $this->createMock(AfterProcessingMessageEvent::class);
        $event->expects(self::once())->method('getConsumer')->willReturn($consumer);

        self::assertInstanceOf(
            AMQPListener::class,
            $this->buildService($callable)->onAfterProcessingMessage($event)
        );
    }

    public function testOnAfterProcessingMessageTaintableWorkerTainted()
    {
        $called = false;
        $callable = function () use (&$called) {
            $called = true;
        };

        $consumer = new class implements ConsumerInterface, TaintableInterface
        {
            public function execute(AMQPMessage $msg)
            {
                // TODO: Implement execute() method.
            }

            public function enableTaintedFlag(): TaintableInterface
            {
                return $this;
            }

            public function tellMeIfYouAreTainted(callable $callback): TaintableInterface
            {
                $callback(true);
                return $this;
            }
        };

        $event = $this->createMock(AfterProcessingMessageEvent::class);
        $event->expects(self::once())->method('getConsumer')->willReturn($consumer);

        self::assertInstanceOf(
            AMQPListener::class,
            $this->buildService($callable)->onAfterProcessingMessage($event)
        );

        self::assertTrue($called);
    }
}