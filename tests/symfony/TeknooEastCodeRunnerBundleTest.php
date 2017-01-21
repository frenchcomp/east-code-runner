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
use Teknoo\East\CodeRunnerBundle\DependencyInjection\RunnerCompilerPass;
use Teknoo\East\CodeRunnerBundle\DependencyInjection\TaskManagerCompilerPass;
use Teknoo\East\CodeRunnerBundle\TeknooEastCodeRunnerBundle;

/**
 * Class TeknooEastCodeRunnerBundleTest.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers \Teknoo\East\CodeRunnerBundle\TeknooEastCodeRunnerBundle
 */
class TeknooEastCodeRunnerBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return TeknooEastCodeRunnerBundle
     */
    public function buildBundle()
    {
        return new TeknooEastCodeRunnerBundle();
    }

    public function testBuild()
    {
        $containerMock = $this->createMock(ContainerBuilder::class);

        $containerMock
            ->expects(self::exactly(2))
            ->method('addCompilerPass')
            ->withConsecutive([new RunnerCompilerPass()], new TaskManagerCompilerPass());

        $this->buildBundle()->build($containerMock);
    }
}
