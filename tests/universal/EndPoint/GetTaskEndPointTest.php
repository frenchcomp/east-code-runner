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

namespace Teknoo\Tests\East\CodeRunner\EndPoint;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Teknoo\East\CodeRunner\EndPoint\GetTaskEndPoint;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksRegistryInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\East\Foundation\Http\ClientInterface;

/**
 * @covers \Teknoo\East\CodeRunner\EndPoint\GetTaskEndPoint
 */
class GetTaskEndPointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TasksRegistryInterface
     */
    private $tasksRegistry;

    /**
     * @return TasksRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getTasksRegistryMock(): TasksRegistryInterface
    {
        if (!$this->tasksRegistry instanceof TasksRegistryInterface) {
            $this->tasksRegistry = $this->createMock(TasksRegistryInterface::class);
        }

        return $this->tasksRegistry;
    }

    /**
     * @return GetTaskEndPoint
     */
    public function buildEndPoint()
    {
        return new GetTaskEndPoint($this->getTasksRegistryMock());
    }

    public function testNoTaskFound()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('responseFromController')
            ->with($this->callback(function (ResponseInterface $response) {return 404 == $response->getStatusCode();}))
            ->willReturnSelf();

        $this->getTasksRegistryMock()
            ->expects(self::any())
            ->method('get')
            ->with(123)
            ->willThrowException(new \DomainException());

        $endpoint = $this->buildEndPoint();

        self::assertInstanceOf(
            GetTaskEndPoint::class,
            $endpoint(
                $this->createMock(ServerRequestInterface::class),
                $client,
                123
            )
        );
    }

    public function testOk()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('responseFromController')
            ->with($this->callback(function (ResponseInterface $response) {return 200 == $response->getStatusCode();}))
            ->willReturnSelf();

        $task = $this->createMock(TaskInterface::class);

        $this->getTasksRegistryMock()
            ->expects(self::any())
            ->method('get')
            ->with(123)
            ->willReturn($task);

        $endpoint = $this->buildEndPoint();

        self::assertInstanceOf(
            GetTaskEndPoint::class,
            $endpoint(
                $this->createMock(ServerRequestInterface::class),
                $client,
                123
            )
        );
    }
}