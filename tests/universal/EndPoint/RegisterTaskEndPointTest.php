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
use Psr\Http\Message\StreamInterface;
use Teknoo\East\CodeRunner\EndPoint\RegisterTaskEndPoint;
use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\East\CodeRunner\Task\PHPCode;
use Teknoo\East\Foundation\Http\ClientInterface;
use Teknoo\East\Foundation\Promise\PromiseInterface;

/**
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
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
     * @var RunnerManagerInterface
     */
    private $runnerManager;

    /**
     * @return RunnerManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getRunnerManagerMock(): RunnerManagerInterface
    {
        if (!$this->runnerManager instanceof RunnerManagerInterface) {
            $this->runnerManager = $this->createMock(RunnerManagerInterface::class);
        }

        return $this->runnerManager;
    }
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
        return (new RegisterTaskEndPoint($this->getRunnerManagerMock()))
            ->registerTaskManager($this->getTasksManagerMock());
    }

    public function testTaskError()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('responseFromController')
            ->with($this->callback(function (ResponseInterface $response) {
                return 501 == $response->getStatusCode();
            }))
            ->willReturnSelf();

        $this->getTasksManagerMock()
            ->expects(self::any())
            ->method('executeMe')
            ->willReturnCallback(
                function (TaskInterface $task, PromiseInterface $promise) {
                    $promise->fail(new \Exception());

                    return $this->getTasksManagerMock();
                }
            );

        $endpoint = $this->buildEndPoint();

        $body = $this->createMock(StreamInterface::class);
        $code = \json_encode(new PHPCode('<?php echo "123";', []));
        $body->expects(self::any())->method('__toString')->willReturn($code);
        $body->expects(self::any())->method('getContents')->willReturn($code);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::any())->method('getBody')->willReturn($body);

        self::assertInstanceOf(
            RegisterTaskEndPoint::class,
            $endpoint(
                $request,
                $client
            )
        );
    }

    public function testTaskBadInput()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('responseFromController')
            ->with($this->callback(function (ResponseInterface $response) {
                return 501 == $response->getStatusCode();
            }))
            ->willReturnSelf();

        $endpoint = $this->buildEndPoint();

        $body = $this->createMock(StreamInterface::class);
        $code = "123";
        $body->expects(self::any())->method('__toString')->willReturn($code);
        $body->expects(self::any())->method('getContents')->willReturn($code);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::any())->method('getBody')->willReturn($body);

        self::assertInstanceOf(
            RegisterTaskEndPoint::class,
            $endpoint(
                $request,
                $client
            )
        );
    }

    public function testTaskBadJson()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('responseFromController')
            ->with($this->callback(function (ResponseInterface $response) {
                return 501 == $response->getStatusCode();
            }))
            ->willReturnSelf();

        $endpoint = $this->buildEndPoint();

        $body = $this->createMock(StreamInterface::class);
        $code = "[]";
        $body->expects(self::any())->method('__toString')->willReturn($code);
        $body->expects(self::any())->method('getContents')->willReturn($code);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::any())->method('getBody')->willReturn($body);

        self::assertInstanceOf(
            RegisterTaskEndPoint::class,
            $endpoint(
                $request,
                $client
            )
        );
    }

    public function testOk()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('responseFromController')
            ->with($this->callback(function (ResponseInterface $response) {
                return 200 == $response->getStatusCode();
            }))
            ->willReturnSelf();

        $this->getTasksManagerMock()
            ->expects(self::any())
            ->method('executeMe')
            ->willReturnCallback(
                function (TaskInterface $task, PromiseInterface $promise) {
                    $task->registerUrl('http://foo.bar');

                    $promise->success($task);

                    return $this->getTasksManagerMock();
                }
            );

        $endpoint = $this->buildEndPoint();

        $body = $this->createMock(StreamInterface::class);
        $code = \json_encode(new PHPCode('<?php echo "123";', []));
        $body->expects(self::any())->method('__toString')->willReturn($code);
        $body->expects(self::any())->method('getContents')->willReturn($code);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects(self::any())->method('getBody')->willReturn($body);

        self::assertInstanceOf(
            RegisterTaskEndPoint::class,
            $endpoint(
                $request,
                $client
            )
        );
    }
}
