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
use Symfony\Component\DependencyInjection\Reference;
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
        if (!empty($config['runners'])) {
            //To load automatically the PHP 7 runner
            $loader->load('php7_runner_server.yml');
            $loader->load('php7_runner_worker.yml');
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

        // process the configuration of TeknooEastCodeRunnerExtension
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
                                'dir' => '%kernel.root_dir%/../vendor/teknoo/east-code-runner/src/universal/config/doctrine',
                                'is_bundle' => false,
                                'prefix' => 'Teknoo\East\CodeRunner\Entity',
                            ],
                        ],
                        'auto_mapping' => false,
                        'filters' => [
                            'softdeleteable' => [
                                'class' => SoftDeleteableFilter::class,
                                'enabled' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $container->prependExtensionConfig('stof_doctrine_extensions', [
            'orm' => [
                $doctrineConnection => [
                    'timestampable' => true,
                ],
            ],
        ]);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function configureOldSoundRabbitMQ(ContainerBuilder $container)
    {
        // process the configuration of TeknooEastCodeRunnerExtension
        $configs = $container->getExtensionConfig($this->getAlias());
        // use the Configuration class to generate a config array with the settings "teknoo_east_code_runner"
        $config = $this->processConfiguration(new Configuration(), $configs);

        // check if the configuration define the dbal connection to use with this bundle
        $runnersConfiguration = [];
        if (isset($config['runners']) && \is_array($config['runners'])) {
            $runnersConfiguration = $config['runners'];
        }

        $producers = [];
        $consumers = [];
        foreach ($runnersConfiguration as $runnerId => $runnerConfiguration) {
            //Extract value from configuration from this runner or set with default value suffixed by the runner id
            $amqpConnection = 'default';
            if (!empty($runnerConfiguration['amqp_connection'])) {
                $amqpConnection = $runnerConfiguration['amqp_connection'];
            }

            $taskExchange = 'remote_php7_task_'.$runnerId;
            if (!empty($runnerConfiguration['task_exchange'])) {
                $taskExchange = $runnerConfiguration['task_exchange'];
            }

            $resultExchange = 'remote_php7_result_'.$runnerId;
            if (!empty($runnerConfiguration['result_exchange'])) {
                $resultExchange = $runnerConfiguration['result_exchange'];
            }

            //To autoconfigure server side (Task producer and Result consumer) in the Old Sound RabbitMq bundle
            //And create new service, extending Code runner side service to interact with them
            if (!empty($runnerConfiguration['enable_server'])) {
                $runnerServiceId = 'teknoo.coderunner.runner.remote_php7.'.$runnerId;
                $producerTaskAliasId = 'teknoo.coderunner.vendor.old_sound_producer.remote_php7.task.'.$runnerId;
                $returnConsumerServiceId = 'teknoo.coderunner.service.rabbit_mq_return_consumer.'.$runnerId;

                //AMQP Producer
                $producers['remote_php7_task_'.$runnerId] = [
                    'connection' => $amqpConnection,
                    'exchange_options' => ['name' => $taskExchange, 'type' => 'direct', 'auto_delete' => false],
                    'service_alias' => $producerTaskAliasId,
                ];

                //AMQP CONSUMER
                $consumers['consumer_php7_return_'.$runnerId] = [
                    'connection' => $amqpConnection,
                    'exchange_options' => [
                        'name' => $resultExchange,
                        'type' => 'direct',
                        'auto_delete' => false,
                    ],
                    'queue_options' => [
                        'name' => $resultExchange,
                        'auto_delete' => false,
                    ],
                    'callback' => $returnConsumerServiceId,
                ];

                //Runner service
                $runnerDefinition = new DefinitionDecorator(
                    'teknoo.east.bundle.coderunner.runner.remote_php7.abstract'
                );
                $runnerDefinition->replaceArgument(0, new Reference($producerTaskAliasId));
                $runnerDefinition->replaceArgument(1, $runnerId);
                //Add tags to register them into registry
                $runnerDefinition->addTag('teknoo.east.code_runner.runner.service');
                $container->setDefinition($runnerServiceId, $runnerDefinition);

                //Return Consumer Service
                $returnConsumerDefinition = new DefinitionDecorator(
                    'teknoo.east.bundle.coderunner.service.rabbit_mq_return_consumer.abstract'
                );
                $returnConsumerDefinition->replaceArgument(1, new Reference($runnerServiceId));
                $container->setDefinition($returnConsumerServiceId, $returnConsumerDefinition);
            }

            //To autoconfigure worker side (Task consumer and Result Producer) in the Old Sound RabbitMq bundle
            //And create new service, extending Code runner side service to interact with them
            if (!empty($runnerConfiguration['enable_worker'])) {
                $producerReturnAliasId = 'teknoo.coderunner.vendor.old_sound_producer.remote_php7.return_'.$runnerId;
                $composerCommandDefinitionId = 'teknoo.coderunner.worker.vendor.shell.composer.command.'.$runnerId;
                $phpCommandDefinitionId = 'teknoo.coderunner.worker.vendor.shell.php_commander.command.'.$runnerId;
                $gaufretteAdapterDefinitionId = 'teknoo.coderunner.worker.vendor.gaufrette.adapter.'.$runnerId;
                $gaufretteFileSystemDefinitionId = 'teknoo.coderunner.worker.vendor.gaufrette.filesystem.'.$runnerId;
                $composerConfigurationDefinitionId = 'teknoo.coderunner.worker.composer.configuration.'.$runnerId;
                $phpCommanderDefinitionId = 'teknoo.coderunner.worker.php_commander.'.$runnerId;
                $phpRunnerDefinitionId = 'teknoo.coderunner.worker.php7_runner.'.$runnerId;

                $composerCommandValue = 'composer';
                if (!empty($runnerConfiguration['composer_command'])) {
                    $composerCommandValue = $runnerConfiguration['composer_command'];
                }

                $composerInstructionValue = 'install';
                if (!empty($runnerConfiguration['composer_instruction'])) {
                    $composerInstructionValue = $runnerConfiguration['composer_instruction'];
                }

                $phpCommandValue = 'php';
                if (!empty($runnerConfiguration['php_command'])) {
                    $phpCommandValue = $runnerConfiguration['php_command'];
                }

                $workDirectory = '/tmp';
                if (!empty($runnerConfiguration['work_directory'])) {
                    $workDirectory = $runnerConfiguration['work_directory'];
                }

                $producers['remote_php7_return_'.$runnerId] = [
                    'connection' => $amqpConnection,
                    'exchange_options' => ['name' => $resultExchange, 'type' => 'direct', 'auto_delete' => false],
                    'service_alias' => $producerReturnAliasId,
                ];

                $consumers['consumer_php7_task_'.$runnerId] = [
                    'connection' => $amqpConnection,
                    'exchange_options' => [
                        'name' => $taskExchange,
                        'type' => 'direct',
                        'auto_delete' => false,
                    ],
                    'queue_options' => [
                        'name' => $taskExchange,
                        'auto_delete' => false,
                    ],
                    'callback' => $phpRunnerDefinitionId,
                ];

                //Composer command service
                $composerCommandDefinition = new DefinitionDecorator(
                    'teknoo.east.bundle.coderunner.worker.vendor.shell.composer.command.abstract'
                );
                $composerCommandDefinition->replaceArgument(0, $composerCommandValue);
                $container->setDefinition($composerCommandDefinitionId, $composerCommandDefinition);

                //Composer command service
                $phpCommandDefinition = new DefinitionDecorator(
                    'teknoo.east.bundle.coderunner.worker.vendor.shell.php_commander.command.abstract'
                );
                $phpCommandDefinition->replaceArgument(0, $phpCommandValue);
                $container->setDefinition($phpCommandDefinitionId, $phpCommandDefinition);

                //Gaufrette Adapter
                $gaufretteAdapterDefinition = new DefinitionDecorator(
                    'teknoo.east.bundle.coderunner.worker.vendor.gaufrette.adapter.abstract'
                );
                $gaufretteAdapterDefinition->replaceArgument(0, $workDirectory);
                $container->setDefinition($gaufretteAdapterDefinitionId, $gaufretteAdapterDefinition);

                //Gaufrette FileSystem
                $gaufretteFileSystemDefinition = new DefinitionDecorator(
                    'teknoo.east.bundle.coderunner.worker.vendor.gaufrette.filesystem.abstract'
                );
                $gaufretteFileSystemDefinition->replaceArgument(0, new Reference($gaufretteAdapterDefinitionId));
                $container->setDefinition($gaufretteFileSystemDefinitionId, $gaufretteFileSystemDefinition);

                //Composer Configurator
                $composerConfiguratorDefinition = new DefinitionDecorator(
                    'teknoo.east.bundle.coderunner.worker.composer.configuration.abstract'
                );
                $composerConfiguratorDefinition->replaceArgument(1, new Reference($composerCommandDefinitionId));
                $composerConfiguratorDefinition->replaceArgument(2, new Reference($gaufretteFileSystemDefinitionId));
                $composerConfiguratorDefinition->replaceArgument(3, $composerInstructionValue);
                $composerConfiguratorDefinition->replaceArgument(4, $workDirectory);
                $container->setDefinition($composerConfigurationDefinitionId, $composerConfiguratorDefinition);

                //PHP Commander
                $phpCommanderDefinition = new DefinitionDecorator(
                    'teknoo.east.bundle.coderunner.worker.php_commander.abstract'
                );
                $phpCommanderDefinition->replaceArgument(1, new Reference($phpCommandDefinitionId));
                $phpCommanderDefinition->replaceArgument(2, new Reference($gaufretteFileSystemDefinitionId));
                $phpCommanderDefinition->replaceArgument(4, $workDirectory);
                $container->setDefinition($phpCommanderDefinitionId, $phpCommanderDefinition);

                //PHP Runner
                $runnerDefinition = new DefinitionDecorator(
                    'teknoo.east.bundle.coderunner.worker.php7_runner.abstract'
                );
                $runnerDefinition->replaceArgument(0, new Reference($producerReturnAliasId));
                $runnerDefinition->replaceArgument(3, new Reference($composerConfigurationDefinitionId));
                $runnerDefinition->replaceArgument(4, new Reference($phpCommanderDefinitionId));
                $container->setDefinition($phpRunnerDefinitionId, $runnerDefinition);
            }
        }

        $container->prependExtensionConfig('old_sound_rabbit_mq', [
            'producers' => $producers,
            'consumers' => $consumers,
        ]);
    }

    public function prepend(ContainerBuilder $container)
    {
        $this->configureDoctrine($container);
        $this->configureOldSoundRabbitMQ($container);
    }
}
