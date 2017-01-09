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

namespace Teknoo\East\CodeRunner\Service;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Runner\RemotePHP7Runner\RemotePHP7Runner;
use Teknoo\East\CodeRunner\Task\Interfaces\StatusInterface;
use Teknoo\East\CodeRunner\Task\Status;

/**
 * Class RabbitMQStatusConsumerService.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class RabbitMQStatusConsumerService implements ConsumerInterface
{
    /**
     * @var RemotePHP7Runner
     */
    private $remotePHP7Runner;

    /**
     * @var RunnerManagerInterface
     */
    private $runnerManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * RabbitMQStatusConsumerService constructor.
     *
     * @param RemotePHP7Runner       $remotePHP7Runner
     * @param RunnerManagerInterface $runnerManager
     * @param LoggerInterface        $logger
     */
    public function __construct(RemotePHP7Runner $remotePHP7Runner, RunnerManagerInterface $runnerManager, LoggerInterface $logger)
    {
        $this->remotePHP7Runner = $remotePHP7Runner;
        $this->runnerManager = $runnerManager;
        $this->logger = $logger;
    }

    /**
     * Method to convert the JSON representation of a StatusInterface object to an instance of this class.
     *
     * @param AMQPMessage $message
     *
     * @return StatusInterface
     */
    private function extractStatus(AMQPMessage $message): StatusInterface
    {
        $decodedBody = json_decode($message->body, true);
        if (empty($decodedBody['class']) || !is_callable([$decodedBody['class'], 'jsonDeserialize'])) {
            throw new \RuntimeException('Error, the status representation has no classname');
        }

        /**
         * @var Status::class
         */
        $statusClassName = $decodedBody['class'];

        return $statusClassName::jsonDeserialize($decodedBody);
    }

    /**
     * To analyse and consume a message from RabbitMQ, sent by the remote runner
     * If the task is not managed by this runner, the consumer return false to requeue the message.
     *
     * @param AMQPMessage $msg
     *
     * @return bool
     */
    public function execute(AMQPMessage $msg)
    {
        try {
            $status = $this->extractStatus($msg);

            $this->runnerManager->pushStatus($this->remotePHP7Runner, $status);
        } catch (\Throwable $e) {
            $this->logger->critical($e->getMessage().PHP_EOL.$e->getTraceAsString());

            return false;
        }

        return true;
    }
}
