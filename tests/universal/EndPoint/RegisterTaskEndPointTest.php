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
use Teknoo\East\CodeRunner\EndPoint\RegisterTaskEndPoint;
use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\East\Foundation\Http\ClientInterface;

/**
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers \Teknoo\East\CodeRunner\EndPoint\RegisterTaskEndPoint
 */
class RegisterTaskEndPointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaskManagerInterface
     */
    private $tasksManager;

    /**
     * @return TaskManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getTasksManagerMock(): TaskManagerInterface
    {
        if (!$this->tasksManager instanceof TaskManagerInterface) {
            $this->tasksManager = $this->createMock(TaskManagerInterface::class);
        }

        return $this->tasksManager;
    }

    /**
     * @return RegisterTaskEndPoint
     */
    public function buildEndPoint()
    {
        return new RegisterTaskEndPoint($this->getTasksManagerMock());
    }

    public function testTaskError()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('responseFromController')
            ->with($this->callback(function (ResponseInterface $response) {return 501 == $response->getStatusCode();}))
            ->willReturnSelf();

        $this->getTasksManagerMock()
            ->expects(self::any())
            ->method('executeMe')
            ->willReturnSelf();

        $endpoint = $this->buildEndPoint();

        self::assertInstanceOf(
            RegisterTaskEndPoint::class,
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

        $this->getTasksManagerMock()
            ->expects(self::any())
            ->method('executeMe')
            ->willReturnCallback(
                function (TaskInterface $task) {
                    $task->registerUrl('http://foo.bar');

                    return $this->getTasksManagerMock();
                }
            );

        $endpoint = $this->buildEndPoint();

        self::assertInstanceOf(
            RegisterTaskEndPoint::class,
            $endpoint(
                $this->createMock(ServerRequestInterface::class),
                $client,
                123
            )
        );
    }
}