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

use Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
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
class TeknooEastCodeRunnerExtension extends Extension implements PrependExtensionInterface
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

    /**
     * @param ContainerBuilder $container
     */
    private function configureDoctrine(ContainerBuilder $container)
    {
        $doctrineConnection = 'default';

        // process the configuration of AcmeHelloExtension
        $configs = $container->getExtensionConfig($this->getAlias());
        // use the Configuration class to generate a config array with the settings "teknoo_east_code_runner"
        $config = $this->processConfiguration(new Configuration(), $configs);

        // check if the configuration define the dbal connection to use with this bundle
        if (isset($config['doctrine_connection'])) {
            $doctrineConnection = $config['doctrine_connection'];
        }

        $container->prependExtensionConfig('doctrine', [
            'orm' => [
                'entity_managers' => [
                    'code_runner' => [
                        'connection' => $doctrineConnection,
                        'naming_strategy' => 'doctrine.orm.naming_strategy.underscore',
                        'mappings' => [
                            'TeknooEastCodeRunner' => [
                                'type' => 'yml',
                                'dir' => "%kernel.root_dir%/../vendor/teknoo/east-code-runner/src/universal/config/doctrine",
                                'is_bundle' => false,
                                'prefix' => 'Teknoo\East\CodeRunner\Entity'
                            ]
                        ],
                        'auto_mapping' => false,
                        'filters' => [
                            'softdeleteable' => [
                                'class' => SoftDeleteableFilter::class,
                                'enabled' => false
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $container->prependExtensionConfig('stof_doctrine_extensions', [
            'orm' => [
                $doctrineConnection => [
                    'timestampable' => true
                ]
            ]
        ]);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function configureOldSoundRabbitMQ(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('old_sound_rabbit_mq', [
            'producers' => [
                'remote_php7_task' => [
                    'connection' => 'code_runner',
                    'exchange_options' => ['name' => 'remote_php7_task', 'type' => 'direct', 'auto_delete' => false],
                    'service_alias' => 'teknoo.east.bundle.coderunner.vendor.old_sound_producer.remote_php7.task',
                ],
                'remote_php7_return' => [
                    'connection' => 'code_runner',
                    'exchange_options' => ['name' => 'remote_php7_result', 'type' => 'direct', 'auto_delete' => false],
                    'service_alias' => 'teknoo.east.bundle.coderunner.vendor.old_sound_producer.remote_php7.return',
                ],
            ],
            'consumers' => [
                'worker_php7_task' => [
                    'connection' => 'code_runner',
                    'exchange_options' => [
                        'name' => 'remote_php7_task',
                        'type' => 'direct',
                        'auto_delete' => false,
                    ],
                    'queue_options' => [
                        'name' => 'remote_php7_task',
                        'auto_delete' => false,
                    ],
                    'callback' => 'teknoo.east.bundle.coderunner.worker.php7_runner',
                ],
                'consumer_php7_return' => [
                    'connection' => 'code_runner',
                    'exchange_options' => [
                        'name' => 'remote_php7_result',
                        'type' => 'direct',
                        'auto_delete' => false,
                    ],
                    'queue_options' => [
                        'name' => 'remote_php7_result',
                        'auto_delete' => false,
                    ],
                    'callback' => 'teknoo.east.bundle.coderunner.service.rabbit_mq_return_consumer'
                ],
            ]
        ]);
    }

    public function prepend(ContainerBuilder $container)
    {
        $this->configureDoctrine($container);
        $this->configureOldSoundRabbitMQ($container);
    }
}
