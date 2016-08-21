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
namespace Teknoo\Tests\East\CodeRunnerBundle\Task;

use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskUserInterface;

/**
 * @method assertInstanceof
 * @method createMock
 */
trait TaskUserTestTrait
{
    /**
     * @return TaskUserInterface
     */
    abstract public function buildTaskUserInstance(): TaskUserInterface;

    /**
     * @excepedException \Throwable
     */
    public function testRegisterTaskBadInput()
    {
        $this->buildTaskUserInstance()->registerTask(new \stdClass());
    }

    public function testRegisterTaskOutput()
    {
        self::assertInstanceOf(
            TaskUserInterface::class,
            $this->buildTaskUserInstance()->registerTask(
                $this->createMock(TaskInterface::class)
            )
        );
    }
}