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
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east/coderunner Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
namespace Teknoo\East\CodeRunnerBundle\Service;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Teknoo\East\CodeRunnerBundle\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunnerBundle\Runner\RemotePHP7Runner\RemotePHP7Runner;
use Teknoo\East\CodeRunnerBundle\Task\Status;

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
     * Method to convert the JSON representation of a StatusInterface object to an instance of this class
     * @param AMQPMessage $message
     * @return Status
     */
    private function extractStatus(AMQPMessage $message): Status
    {
        $decodedBody = json_decode($message->body, true);
        if (empty($decodedBody['class']) || !is_callable([$decodedBody['class'], 'jsonDeserialize'])) {
            throw new \RuntimeException('Error, the status representation has no classname');
        }

        /**
         * @var $statusClassName Status::class
         */
        $statusClassName = $decodedBody['class'];

        return $statusClassName::jsonDeserialize($decodedBody);
    }

    /**
     * To analyse and consume a message from RabbitMQ, sent by the remote runner
     * If the task is not managed by this runner, the consumer return false to requeue the message.
     *
     * @param AMQPMessage $msg
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