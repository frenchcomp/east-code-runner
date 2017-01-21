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

namespace Teknoo\Tests\East\CodeRunner\Entity;

use Teknoo\East\CodeRunner\Entity\TaskExecution;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\Tests\East\CodeRunner\Entity\Traits\PopulateEntityTrait;

/**
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers \Teknoo\East\CodeRunner\Entity\TaskExecution
 */
class TaskExecutionTest extends \PHPUnit_Framework_TestCase
{
    use PopulateEntityTrait;

    /**
     * @return TaskExecution
     */
    public function buildEntity(): TaskExecution
    {
        return new TaskExecution();
    }

    public function testGetId()
    {
        self::assertEquals(
            123,
            $this->generateEntityPopulated(['id' => 123])->getId()
        );
    }

    public function testGetRunnerIdentifier()
    {
        self::assertEquals(
            'fooBar',
            $this->generateEntityPopulated(['runnerIdentifier' => 'fooBar'])->getRunnerIdentifier()
        );
    }

    public function testSetRunnerIdentifier()
    {
        $entity = $this->buildEntity();
        self::assertInstanceOf(
            TaskExecution::class,
            $entity->setRunnerIdentifier('fooBar')
        );

        self::assertEquals(
            'fooBar',
            $entity->getRunnerIdentifier()
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testSetRunnerIdentifierExceptionOnBadArgument()
    {
        $this->buildEntity()->setRunnerIdentifier(new \stdClass());
    }

    public function testGetTask()
    {
        $task = $this->createMock(TaskInterface::class);
        self::assertEquals(
            $task,
            $this->generateEntityPopulated(['task' => $task])->getTask()
        );
    }

    public function testSetTask()
    {
        $task = $this->createMock(TaskInterface::class);
        $entity = $this->buildEntity();
        self::assertInstanceOf(
            TaskExecution::class,
            $entity->setTask($task)
        );

        self::assertEquals(
            $task,
            $entity->getTask()
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testSetTaskExceptionOnBadArgument()
    {
        $this->buildEntity()->setTask(new \stdClass());
    }

    public function testGetCreatedAt()
    {
        $date = new \DateTime('2016-07-28');
        self::assertEquals(
            $date,
            $this->generateEntityPopulated(['createdAt' => $date])->getCreatedAt()
        );
    }

    public function testSetCreatedAt()
    {
        $date = new \DateTime('2016-07-28');
        $entity = $this->buildEntity();
        self::assertInstanceOf(
            TaskExecution::class,
            $entity->setCreatedAt($date)
        );

        self::assertEquals(
            $date,
            $entity->getCreatedAt()
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testSetCreatedAtExceptionOnBadArgument()
    {
        $this->buildEntity()->setCreatedAt(new \stdClass());
    }

    public function testGetUpdatedAt()
    {
        $date = new \DateTime('2016-07-28');
        self::assertEquals(
            $date,
            $this->generateEntityPopulated(['updatedAt' => $date])->getUpdatedAt()
        );
    }

    public function testSetUpdatedAt()
    {
        $date = new \DateTime('2016-07-28');
        $entity = $this->buildEntity();
        self::assertInstanceOf(
            TaskExecution::class,
            $entity->setUpdatedAt($date)
        );

        self::assertEquals(
            $date,
            $entity->getUpdatedAt()
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testSetUpdatedAtExceptionOnBadArgument()
    {
        $this->buildEntity()->setUpdatedAt(new \stdClass());
    }

    public function testGetDeletedAt()
    {
        $date = new \DateTime('2016-07-28');
        self::assertEquals(
            $date,
            $this->generateEntityPopulated(['deletedAt' => $date])->getDeletedAt()
        );
    }

    public function testSetDeletedAt()
    {
        $date = new \DateTime('2016-07-28');
        $entity = $this->buildEntity();
        self::assertInstanceOf(
            TaskExecution::class,
            $entity->setDeletedAt($date)
        );

        self::assertEquals(
            $date,
            $entity->getDeletedAt()
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testSetDeletedAtExceptionOnBadArgument()
    {
        $this->buildEntity()->setDeletedAt(new \stdClass());
    }
}
