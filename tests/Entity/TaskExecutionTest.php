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

use Teknoo\East\CodeRunnerBundle\Entity\TaskExecution;
use Teknoo\Tests\East\CodeRunnerBundle\Entity\Traits\PopulateEntityTrait;

/**
 * @covers Teknoo\East\CodeRunnerBundle\Entity\TaskExecution
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
            $this->generateEntityPopulated(['id'=>123])->getId()
        );
    }

    public function testGetRunnerIdentifier()
    {
        self::assertEquals(
            'fooBar',
            $this->generateEntityPopulated(['id'=>123])->getRunnerIdentifier()
        );
    }
}