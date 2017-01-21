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
use Teknoo\East\CodeRunnerBundle\DependencyInjection\TeknooEastCodeRunnerExtension;

/**
 * Class TeknooStatesExtensionTest.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @covers \Teknoo\East\CodeRunnerBundle\DependencyInjection\TeknooEastCodeRunnerExtension
 */
class TeknooEastCodeRunnerExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return TeknooEastCodeRunnerExtension
     */
    public function buildExtension()
    {
        return new TeknooEastCodeRunnerExtension();
    }

    public function testLoadEmpty()
    {
        $containerMock = $this->createMock(ContainerBuilder::class);

        $containerMock
            ->expects(self::any())
            ->method('hasExtension')
            ->willReturn(true);

        $this->buildExtension()->load([], $containerMock);
    }

    public function testLoadFull()
    {
        $containerMock = $this->createMock(ContainerBuilder::class);

        $containerMock
            ->expects(self::any())
            ->method('hasExtension')
            ->willReturn(true);

        $this->buildExtension()->load(
            [
                [
                    'php7_runner' => [
                        'enable_server' => true,
                        'enable_worker' => true,
                        'work_directory' => '/tmp/php7-runner',
                        'composer_command' => 'composer',
                        'composer_instruction' => 'install',
                        'php_command' => 'php',
                    ],
                    'tasks_managers' => [
                        'default' => [
                            'identifier' => 'default.foobar',
                            'url_pattern' => 'http://foo/UUID',
                            'service_id' => 'manager.default',
                            'is_default' => true,
                        ],
                        'another' => [
                            'identifier' => 'another.foobar',
                            'url_pattern' => 'http://foo/UUID',
                            'service_id' => 'manager.another',
                            'is_default' => false,
                        ],
                    ],
                ],
            ],
            $containerMock
        );
    }
}
