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
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
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

        //To configure PHP7 Runner service
        if (!empty($config['php7_runner'])) {
            $php7RunnerConfig = $config['php7_runner'];

            //To load automatically the PHP 7 runner
            if (!empty($php7RunnerConfig['enable_server'])) {
                $loader->load('php7_runner_server.yml');
            }

            if (!empty($php7RunnerConfig['enable_worker'])) {
                $loader->load('php7_runner_worker.yml');
            }

            if (!empty($php7RunnerConfig['work_directory'])) {
                $container->setParameter(
                    'teknoo.east.bundle.coderunner.worker.work_directory',
                    $php7RunnerConfig['work_directory']
                );
            }

            if (!empty($php7RunnerConfig['composer_command'])) {
                $container->setParameter(
                    'teknoo.east.bundle.coderunner.worker.composer.configuration.command',
                    $php7RunnerConfig['composer_command']
                );
            }

            if (!empty($php7RunnerConfig['composer_instruction'])) {
                $container->setParameter(
                    'teknoo.east.bundle.coderunner.worker.composer.configuration.instruction',
                    $php7RunnerConfig['composer_instruction']
                );
            }

            if (!empty($php7RunnerConfig['php_command'])) {
                $container->setParameter(
                    'teknoo.east.bundle.coderunner.worker.php_commander.command',
                    $php7RunnerConfig['php_command']
                );
            }
        }

        //To define tasks managers
        if (!empty($config['tasks_managers'])) {
            foreach ($config['tasks_managers'] as $definitionValues) {
                //Extends abstract
                $taskManagerDef = new DefinitionDecorator('teknoo.east.bundle.coderunner.manager.tasks.abstract');
                $taskManagerDef->replaceArgument(0, $definitionValues['identifier']);
                $taskManagerDef->replaceArgument(1, $definitionValues['url_pattern']);
                //Add tags to register them into registry
                $taskManagerDef->addTag('teknoo.east.code_runner.task_manager');
                $container->setDefinition($definitionValues['service_id'], $taskManagerDef);

                //To use this manager into registry end point
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
