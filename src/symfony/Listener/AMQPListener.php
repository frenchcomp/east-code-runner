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

namespace Teknoo\East\CodeRunnerBundle\Listener;

use OldSound\RabbitMqBundle\Event\AfterProcessingMessageEvent;
use Teknoo\East\CodeRunner\Worker\Interfaces\TaintableInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see
 *  {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class AMQPListener
{
    /**
     * @var callable
     */
    private $methodToCall;

    /**
     * AMQPListener constructor.
     *
     * @param callable $methodToCall
     */
    public function __construct(callable $methodToCall)
    {
        $this->methodToCall = $methodToCall;
    }

    /**
     * To kill the worker if it's tainted.
     *
     * @param AfterProcessingMessageEvent $event
     *
     * @return AMQPListener
     */
    public function onAfterProcessingMessage(AfterProcessingMessageEvent $event): AMQPListener
    {
        $consumer = $event->getConsumer();

        if ($consumer instanceof TaintableInterface) {
            $consumer->tellMeIfYouAreTainted(function ($isTainted) {
                $callback = $this->methodToCall;
                if (!empty($isTainted) && \is_callable($callback)) {
                    $callback('Worker tainted');
                }
            });
        }

        return $this;
    }
}
