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

namespace Teknoo\Tests\East\CodeRunner\Registry;

use Teknoo\East\CodeRunner\Registry\Interfaces\TasksRegistryInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\East\Foundation\Promise\PromiseInterface;

/**
 * Class AbstractTasksRegistryTest.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
abstract class AbstractTasksRegistryTest extends \PHPUnit_Framework_TestCase
{
    abstract public function buildRegistry(): TasksRegistryInterface;

    public function testGetNotFound()
    {
        $promise = $this->createMock(PromiseInterface::class);
        $promise->expects(self::once())
            ->method('fail')
            ->willReturnCallback(function ($exception) use ($promise) {
                self::assertInstanceOf(\DomainException::class, $exception);

                return $promise;
            });

        self::assertInstanceOf(
            TasksRegistryInterface::class,
            $this->buildRegistry()->get('barFoo', $promise)
        );
    }

    public function testGet()
    {
        $registry = $this->buildRegistry();

        $promise = $this->createMock(PromiseInterface::class);
        $promise->expects(self::once())
            ->method('success')
            ->willReturnCallback(function ($task) use ($promise) {
                self::assertInstanceOf(TaskInterface::class, $task);

                return $promise;
            });

        self::assertInstanceOf(
            TasksRegistryInterface::class,
            $registry->get('fooBar', $promise)
        );
    }
}
