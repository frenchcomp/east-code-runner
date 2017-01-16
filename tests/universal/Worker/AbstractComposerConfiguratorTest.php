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

namespace Teknoo\Tests\East\CodeRunner\Worker;

use Teknoo\East\CodeRunner\Runner\Capability;
use Teknoo\East\CodeRunner\Task\Interfaces\CodeInterface;
use Teknoo\East\CodeRunner\Worker\Interfaces\ComposerConfiguratorInterface;
use Teknoo\East\CodeRunner\Worker\Interfaces\RunnerInterface;

/**
 * Class AbstractComposerConfiguratorTest
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
abstract class AbstractComposerConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    abstract public function buildConfigurator(): ComposerConfiguratorInterface;

    public function testResetReturn()
    {
        self::assertInstanceOf(
            ComposerConfiguratorInterface::class,
            $this->buildConfigurator()->reset()
        );
    }

    /**
     * @expectedException \Throwable
     */
    public function testConfigureBadCode()
    {
        $this->buildConfigurator()->configure(new \stdClass(), $this->createMock(RunnerInterface::class));
    }

    /**
     * @expectedException \Throwable
     */
    public function testConfigureBadRunner()
    {
        $this->buildConfigurator()->configure($this->createMock(CodeInterface::class), new \stdClass());
    }

    public function testConfigure()
    {
        $code = $this->createMock(CodeInterface::class);
        $code->expects(self::any())->method('getNeededCapabilities')->willReturn([
            new Capability('foo', '2.3.4'),
            new Capability('bar', '*')
        ]);

        $runner = $this->createMock(RunnerInterface::class);
        $runner->expects(self::once())->method('composerIsReady')->willReturnSelf();

        self::assertInstanceOf(
            ComposerConfiguratorInterface::class,
            $this->buildConfigurator()->configure(
                $code,
                $runner
            )
        );
    }
}
