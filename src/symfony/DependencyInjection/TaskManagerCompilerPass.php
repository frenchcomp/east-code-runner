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
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east/coderunner Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Teknoo\East\CodeRunnerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TaskManagerCompilerPass.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class TaskManagerCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     *
     * @throws \Exception when a tag is incomplete
     */
    private function registerManagerIntoRegistry(ContainerBuilder $container)
    {
        if (!$container->has('teknoo.east.bundle.coderunner.registry.tasks_manager_by_task')) {
            //Skip if the service does not exist
            return;
        }

        $registryId = 'teknoo.east.bundle.coderunner.registry.tasks_manager_by_task';

        $taggedServices = $container->findTaggedServiceIds('teknoo.east.code_runner.task_manager');

        $endPointDeleteTaskDefinition = null;
        if ($container->has('teknoo.east.bundle.coderunner.endpoint.delete_task')) {
            $endPointDeleteTaskDefinition = $container->findDefinition(
                'teknoo.east.bundle.coderunner.endpoint.delete_task'
            );
        }

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $managerDefinition = $container->findDefinition($id);

                $managerDefinition->addMethodCall(
                    'registerIntoMe',
                    [new Reference($registryId)]
                );

                if ($endPointDeleteTaskDefinition instanceof Definition) {
                    $endPointDeleteTaskDefinition->addMethodCall(
                        'registerTaskManager',
                        [new Reference($id)]
                    );
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->registerManagerIntoRegistry($container);

        return $this;
    }
}
