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
use Teknoo\East\CodeRunnerBundle\DependencyInjection\TaskManagerCompilerPass;

/**
 * Class TaskManagerCompilerPassTest.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers \Teknoo\East\CodeRunnerBundle\DependencyInjection\TaskManagerCompilerPass
 */
class TaskManagerCompilerPassTest extends \PHPUnit_Framework_TestCase
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
     * @return TaskManagerCompilerPass
     */
    public function buildCompilerPass()
    {
        return new TaskManagerCompilerPass();
    }

    public function testProcess()
    {
        $def = $this->createMock(Definition::class);
        $def->expects($this->exactly(4))->method('addMethodCall')->willReturnSelf();

        $this->getContainerBuilderMock()
            ->expects(self::any())
            ->method('findTaggedServiceIds')
            ->with('teknoo.east.code_runner.task_manager')
            ->willReturn([
                'service1' => ['foo' => 'bar'],
                'service2' => ['bar' => 'foo'],
            ]);

        $this->getContainerBuilderMock()
            ->expects(self::any())
            ->method('has')
            ->withConsecutive(
                ['teknoo.east.bundle.coderunner.registry.tasks_manager_by_task']
            )
            ->willReturn(true);

        $this->getContainerBuilderMock()
            ->expects(self::exactly(2))
            ->method('findDefinition')
            ->withConsecutive(
                ['teknoo.east.bundle.coderunner.registry.tasks_manager_by_task'],
                ['teknoo.east.bundle.coderunner.endpoint.register_task']
            )
            ->willReturn($def);

        self::assertInstanceOf(
            TaskManagerCompilerPass::class,
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
            ->with('teknoo.east.bundle.coderunner.registry.tasks_manager_by_task')
            ->willReturn(false);

        $this->getContainerBuilderMock()
            ->expects(self::never())
            ->method('findDefinition');

        self::assertInstanceOf(
            TaskManagerCompilerPass::class,
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