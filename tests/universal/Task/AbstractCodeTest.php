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

use Teknoo\East\CodeRunner\Task\Interfaces\CodeInterface;

/**
 * Class AbstractCodeTest
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
abstract class AbstractCodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * To get an instance of the class to test.
     *
     * @return CodeInterface
     */
    abstract public function buildCode(): CodeInterface;

    public function testGetCodeReturn()
    {
        self::assertInternalType(
            'string',
            $this->buildCode()->getCode()
        );
    }

    public function testGetNeededCapabilitiesReturn()
    {
        self::assertInternalType(
            'array',
            $this->buildCode()->getNeededCapabilities()
        );
    }

    /**
     * @expectedException \Teknoo\Immutable\Exception\ImmutableException
     */
    public function testValueObjectBehaviorSetException()
    {
        $this->buildCode()->foo = 'bar';
    }

    /**
     * @expectedException \Teknoo\Immutable\Exception\ImmutableException
     */
    public function testValueObjectBehaviorUnsetException()
    {
        unset($this->buildCode()->foo);
    }

    /**
     * @expectedException \Teknoo\Immutable\Exception\ImmutableException
     */
    public function testValueObjectBehaviorConstructor()
    {
        $this->buildCode()->__construct();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testJsonDeserializeEmptyClass()
    {
        $code = $this->buildCode();
        $className = get_class($code);
        $className::jsonDeserialize([]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testJsonDeserializeBadClass()
    {
        $code = $this->buildCode();
        $className = get_class($code);
        $className::jsonDeserialize(['class' => '\DateTime']);
    }

    public function testJsonEncodeDecode()
    {
        $code = $this->buildCode();
        $className = get_class($code);
        self::assertEquals(
            $code,
            $className::jsonDeserialize(json_decode(json_encode($code), true))
        );
    }
}
