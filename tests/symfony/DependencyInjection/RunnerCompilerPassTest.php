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

namespace Teknoo\Tests\East\CodeRunnerBundle\DependencyInjection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Teknoo\East\CodeRunnerBundle\DependencyInjection\RunnerCompilerPass;

/**
 * Class RunnerCompilerPassTest.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers \Teknoo\East\CodeRunnerBundle\DependencyInjection\RunnerCompilerPass
 */
class RunnerCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @return ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getContainerBuilderMock()
    {
        if (!$this->container instanceof ContainerBuilder) {
            $this->container = $this->createMock(ContainerBuilder::class);
        }

        return $this->container;
    }

    /**
     * @return RunnerCompilerPass
     */
    public function buildCompilerPass()
    {
        return new RunnerCompilerPass();
    }

    public function testProcess()
    {
        $def = $this->createMock(Definition::class);
        $def->expects($this->exactly(2))->method('addMethodCall')->willReturnSelf();

        $this->getContainerBuilderMock()
            ->expects(self::any())
            ->method('findTaggedServiceIds')
            ->with('teknoo.east.code_runner.runner.service')
            ->willReturn([
                'service1' => ['foo' => 'bar'],
                'service2' => ['bar' => 'foo'],
            ]);

        $this->getContainerBuilderMock()
            ->expects(self::once())
            ->method('has')
            ->with('teknoo.east.bundle.coderunner.manager.runners')
            ->willReturn(true);

        $this->getContainerBuilderMock()
            ->expects(self::once())
            ->method('findDefinition')
            ->with('teknoo.east.bundle.coderunner.manager.runners')
            ->willReturn($def);

        self::assertInstanceOf(
            RunnerCompilerPass::class,
            $this->buildCompilerPass()->process(
                $this->getContainerBuilderMock()
            )
        );
    }

    public function testProcessNoService()
    {
        $this->getContainerBuilderMock()
            ->expects(self::never())
            ->method('findTaggedServiceIds');

        $this->getContainerBuilderMock()
            ->expects(self::once())
            ->method('has')
            ->with('teknoo.east.bundle.coderunner.manager.runners')
            ->willReturn(false);

        $this->getContainerBuilderMock()
            ->expects(self::never())
            ->method('findDefinition');

        self::assertInstanceOf(
            RunnerCompilerPass::class,
            $this->buildCompilerPass()->process(
                $this->getContainerBuilderMock()
            )
        );
    }

    /**
     * @expectedException \TypeError
     */
    public function testProcessError()
    {
        $this->buildCompilerPass()->process(new \stdClass());
    }
}