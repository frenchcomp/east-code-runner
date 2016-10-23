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
namespace Teknoo\Tests\East\CodeRunnerBundle\Runner;

use Teknoo\East\CodeRunnerBundle\Runner\Capability;
use Teknoo\East\CodeRunnerBundle\Runner\Interfaces\CapabilityInterface;

/**
 * @covers \Teknoo\East\CodeRunnerBundle\Runner\Capability
 */
class CapabilityTest extends AbstractCapabilityTest
{
    /**
     * @return CapabilityInterface|Capability
     */
    public function buildCapacity(): CapabilityInterface
    {
        return new Capability('foo', 'bar');
    }

    /**
     * @expectedException \Teknoo\Immutable\Exception\ImmutableException
     */
    public function testValueObjectBehaviorConstructor()
    {
        $this->buildCapacity()->__construct('foo', 'bar');
    }

    public function testGetValueReturnNull()
    {
        self::assertNull((new Capability('foo', null))->getValue());
    }
}