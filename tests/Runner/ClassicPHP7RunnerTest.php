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
use Teknoo\East\CodeRunnerBundle\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunnerBundle\Runner\ClassicPHP7Runner\ClassicPHP7Runner;

/**
 * Class ClassicPHP7RunnerTest
 * @covers Teknoo\East\CodeRunnerBundle\Runner\ClassicPHP7Runner\ClassicPHP7Runner
 */
class ClassicPHP7RunnerTest extends AbstractRunnerTest
{
    public function buildRunner(): RunnerInterface
    {
        return new ClassicPHP7Runner('ClassicPHP7Runner1', 'ClassicPHP7Runner', 'PHP7.0', [new Capability('feature', 'PHP7')]);
    }

    public function testCanYouExecuteCodeNotRunnableByTHisRunner()
    {
        $this->fail();
    }

    public function testCanYouExecuteCodeInvalid()
    {
        $this->fail();
    }
}