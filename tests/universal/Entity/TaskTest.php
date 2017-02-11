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

use Teknoo\East\CodeRunner\Entity\Task\States\Unregistered;
use Teknoo\East\CodeRunner\Entity\Task\Task;
use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\East\CodeRunner\Task\PHPCode;
use Teknoo\East\CodeRunner\Task\Status;
use Teknoo\East\CodeRunner\Task\TextResult;
use Teknoo\Tests\East\CodeRunner\Entity\Traits\PopulateEntityTrait;
use Teknoo\Tests\East\CodeRunner\Task\AbstractTaskTest;

/**
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers \Teknoo\East\CodeRunner\Entity\Task\Task
 * @covers \Teknoo\East\CodeRunner\Entity\Task\States\Executed
 * @covers \Teknoo\East\CodeRunner\Entity\Task\States\Registered
 * @covers \Teknoo\East\CodeRunner\Entity\Task\States\Unregistered
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

    public function testGetId()
    {
        self::assertEquals(
            123,
            $this->generateEntityPopulated(['id' => 123])->getId()
        );
    }

    public function testSetId()
    {
        $entity = $this->buildTask();
        self::assertInstanceOf(
            Task::class,
            $entity->setId("123")
        );

        self::assertEquals(
            "123",
            $entity->getId()
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testSetIdBadValue()
    {
        $entity = $this->buildTask();
        $entity->setId(new \stdClass());
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
            $this->generateEntityPopulated(['updatedAt' => $date])->getUpdatedAt()
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
            $this->generateEntityPopulated(['deletedAt' => $date])->getDeletedAt()
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

    public function testPostLoadJsonUpdateAlreadyDecoded()
    {
        $code = new PHPCode('<?php phpinfo();', []);
        self::assertEquals(
            $code,
            $this->generateEntityPopulated(['codeInstance' => $code])->postLoadJsonUpdate()->getCode()
        );
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testPostLoadJsonUpdateNoClass()
    {
        $this->generateEntityPopulated(['code' => json_decode(json_encode([]), true)])->postLoadJsonUpdate()->getCode();
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testPostLoadJsonUpdateClassDoesNotExist()
    {
        $this->generateEntityPopulated(['code' => json_encode(['class' => 'fooBar'])])->postLoadJsonUpdate()->getCode();
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testPostLoadJsonUpdateNoCallable()
    {
        $this->generateEntityPopulated(['code' => json_encode(['class' => '\DateTime'])])->postLoadJsonUpdate()->getCode();
    }

    public function testPostLoadJsonUpdateNonDecoded()
    {
        $code = new PHPCode('<?php phpinfo();', []);
        $status = new Status('Test');
        $result = new TextResult('foo', 'bar', '7.0', 12, 23);

        /**
         * @var Task
         */
        $task = $this->generateEntityPopulated([
            'code' => json_encode($code),
            'status' => json_encode($status),
            'result' => json_encode($result),
        ])->postLoadJsonUpdate();

        self::assertEquals(
            $code,
            $task->getCode()
        );

        self::assertEquals(
            $status,
            $task->getStatus()
        );

        self::assertEquals(
            $result,
            $task->getResult()
        );
    }

    public function testPrePersistJsonUpdate()
    {
        $code = new PHPCode('<?php phpinfo();', []);
        $status = new Status('Test');
        $result = new TextResult('foo', 'bar', '7.0', 12, 23);

        /**
         * @var Task
         */
        $task = $this->generateEntityPopulated([
            'codeInstance' => $code,
            'statusInstance' => $status,
            'resultInstance' => $result,
        ])->prePersistJsonUpdate();

        self::assertInstanceOf(Task::class, $task);

        $refObj = new \ReflectionObject($task);

        $refProp = $refObj->getProperty('code');
        $refProp->setAccessible(true);
        self::assertEquals(\json_encode($code), $refProp->getValue($task));

        $refProp = $refObj->getProperty('status');
        $refProp->setAccessible(true);
        self::assertEquals(\json_encode($status), $refProp->getValue($task));

        $refProp = $refObj->getProperty('result');
        $refProp->setAccessible(true);
        self::assertEquals(\json_encode($result), $refProp->getValue($task));
    }

    public function testJsonEncodeDecodeWithTaskFulled()
    {
        $task = $this->buildTask();
        $task->setCreatedAt(new \DateTime('2016-10-29', new \DateTimeZone('UTC')));
        $task->setDeletedAt(new \DateTime('2016-10-31', new \DateTimeZone('UTC')));
        $task->setUpdatedAt(new \DateTime('2016-11-01', new \DateTimeZone('UTC'))); //Halloween haha !
        $task->setCode(new PHPCode('', []));
        $task->registerUrl('http://foo.bar');
        $task->registerStatus(new Status(''));
        $task->registerResult(
            $this->createMock(TaskManagerInterface::class),
            new TextResult('', '', '', 0, 0)
        );
        $task->registerStatus(new Status('Final', true));

        $final = Task::jsonDeserialize(json_decode(json_encode($task), true));

        self::assertEquals($task->getCode(), $final->getCode());
        self::assertEquals($task->getCreatedAt(), $final->getCreatedAt());
        self::assertEquals($task->getDeletedAt(), $final->getDeletedAt());
        self::assertEquals($task->getUpdatedAt(), $final->getUpdatedAt());
        self::assertEquals($task->getStatus(), $final->getStatus());
        self::assertEquals($task->getResult(), $final->getResult());
        self::assertEquals($task->getUrl(), $final->getUrl());
    }

    public function testWakeUp()
    {
        $task = $this->buildTask();
        $taskSerialiazed = \serialize($task);
        $taskExt = \unserialize($taskSerialiazed);

        $taskExt->switchState(Unregistered::class);
    }
}
