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
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class TeknooEastCodeRunnerExtension.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class TeknooEastCodeRunnerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('doctrine.config.yml');

        //To load automatically the PHP 7 runner
        if (!empty($config['enable_php7_runner'])) {
            $loader->load('runner_rabbitmq.yml');
        }

        //To define task manager
        if (!empty($config['tasks_managers'])) {
            foreach ($config['tasks_managers'] as $definitionValues) {
                $taskManagerDefinition = new DefinitionDecorator('teknoo.east.bundle.coderunner.manager.tasks.abstract');
                $taskManagerDefinition->replaceArgument(0, $definitionValues['identifier']);
                $taskManagerDefinition->replaceArgument(1, $definitionValues['url_pattern']);
                $taskManagerDefinition->addTag('teknoo.east.code_runner.task_manager');
                $container->setDefinition($definitionValues['service_id'], $taskManagerDefinition);

                if (!empty($definitionValues['is_default'])) {
                    $container->setAlias(
                        'teknoo.east.bundle.coderunner.manager.tasks.default',
                        $definitionValues['service_id']
                    );
                }
            }
        }
    }
}
