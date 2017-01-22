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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('teknoo_east_code_runner');

        $rootNode
            ->children()
            ->scalarNode('doctrine_connection')->defaultValue('default')->end()
                ->arrayNode('tasks_managers')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('service_id')->isRequired()->end()
                            ->scalarNode('identifier')->isRequired()->end()
                            ->scalarNode('url_pattern')->isRequired()->end()
                            ->booleanNode('is_default')->defaultValue(false)->end()
                        ->end()
                    ->end()//Prototype
                ->end() //tasks_managers
                ->arrayNode('runners')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('type')->defaultValue('php7')->end()
                            ->booleanNode('enable_server')->defaultValue(false)->end()
                            ->scalarNode('amqp_connection')->defaultValue('default')->end()
                            ->scalarNode('task_exchange')->defaultValue('remote_php7_task')->end()
                            ->scalarNode('result_exchange')->defaultValue('remote_php7_result')->end()
                            ->booleanNode('enable_worker')->defaultValue(false)->end()
                            ->scalarNode('work_directory')->defaultValue('/tmp/php7-runner')->end()
                            ->scalarNode('composer_command')->defaultValue('')->end()
                            ->scalarNode('composer_instruction')->defaultValue('install')->end()
                            ->scalarNode('php_command')->defaultValue('')->end()
                        ->end()
                    ->end()//Prototype
                ->end()
            ->end(); //root

        return $treeBuilder;
    }
}
