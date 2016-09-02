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
namespace Teknoo\Tests\East\CodeRunnerBundle\Entity;

use Teknoo\East\CodeRunnerBundle\Entity\Task\Task;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;
use Teknoo\Tests\East\CodeRunnerBundle\Entity\Traits\PopulateEntityTrait;
use Teknoo\Tests\East\CodeRunnerBundle\Task\AbstractTaskTest;

/**
 * @covers Teknoo\East\CodeRunnerBundle\Entity\Task\Task
 * @covers Teknoo\East\CodeRunnerBundle\Entity\Task\States\Executed
 * @covers Teknoo\East\CodeRunnerBundle\Entity\Task\States\Registered
 * @covers Teknoo\East\CodeRunnerBundle\Entity\Task\States\Unregistered
 */
class TaskTest extends AbstractTaskTest
{
    use PopulateEntityTrait;

    /**
     * @return TaskInterface|Task
     */
    public function buildTask(): TaskInterface
    {
        return new Task();
    }

    /**
     * @return Task|TaskInterface
     */
    protected function buildEntity()
    {
        return $this->buildTask();
    }

    public function testGetCreatedAt()
    {
        $date = new \DateTime('2016-07-28');
        self::assertEquals(
            $date,
            $this->generateEntityPopulated(['createdAt'=>$date])->getCreatedAt()
        );
    }

    public function testSetCreatedAt()
    {
        $date = new \DateTime('2016-07-28');
        $entity = $this->buildTask();
        self::assertInstanceOf(
            Task::class,
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
        $this->buildTask()->setCreatedAt(new \stdClass());
    }

    public function testGetUpdatedAt()
    {
        $date = new \DateTime('2016-07-28');
        self::assertEquals(
            $date,
            $this->generateEntityPopulated(['updatedAt'=>$date])->getUpdatedAt()
        );
    }

    public function testSetUpdatedAt()
    {
        $date = new \DateTime('2016-07-28');
        $entity = $this->buildTask();
        self::assertInstanceOf(
            Task::class,
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
        $this->buildTask()->setUpdatedAt(new \stdClass());
    }

    public function testGetDeletedAt()
    {
        $date = new \DateTime('2016-07-28');
        self::assertEquals(
            $date,
            $this->generateEntityPopulated(['deletedAt'=>$date])->getDeletedAt()
        );
    }

    public function testSetDeletedAt()
    {
        $date = new \DateTime('2016-07-28');
        $entity = $this->buildTask();
        self::assertInstanceOf(
            Task::class,
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
        $this->buildTask()->setDeletedAt(new \stdClass());
    }
}