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

namespace Teknoo\Tests\East\CodeRunner\Task;

use Teknoo\East\CodeRunner\Task\Interfaces\StatusInterface;

/**
 * Class AbstractStatusTest.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
abstract class AbstractStatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * To get an instance of the class to test.
     *
     * @return StatusInterface
     */
    abstract public function buildStatus(): StatusInterface;

    public function testGetNameReturn()
    {
        self::assertInternalType(
            'string',
            $this->buildStatus()->getName()
        );
    }

    public function testGetNameFinal()
    {
        self::assertInternalType(
            'bool',
            $this->buildStatus()->isFinal()
        );
    }

    /**
     * @expectedException \Teknoo\Immutable\Exception\ImmutableException
     */
    public function testValueObjectBehaviorSetException()
    {
        $this->buildStatus()->foo = 'bar';
    }

    /**
     * @expectedException \Teknoo\Immutable\Exception\ImmutableException
     */
    public function testValueObjectBehaviorUnsetException()
    {
        unset($this->buildStatus()->foo);
    }

    /**
     * @expectedException \Teknoo\Immutable\Exception\ImmutableException
     */
    public function testValueObjectBehaviorConstructor()
    {
        $this->buildStatus()->__construct();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testJsonDeserializeEmptyClass()
    {
        $status = $this->buildStatus();
        $className = get_class($status);
        $className::jsonDeserialize([]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testJsonDeserializeBadClass()
    {
        $status = $this->buildStatus();
        $className = get_class($status);
        $className::jsonDeserialize(['class' => '\DateTime']);
    }

    public function testJsonEncodeDecode()
    {
        $status = $this->buildStatus();
        $className = get_class($status);
        self::assertEquals(
            $status,
            $className::jsonDeserialize(json_decode(json_encode($status), true))
        );
    }
}
