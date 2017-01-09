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

namespace Teknoo\East\CodeRunner\Worker;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Teknoo\East\CodeRunner\Entity\Task\Task;
use Teknoo\East\CodeRunner\Task\Interfaces\CodeInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\ResultInterface;
use Teknoo\East\CodeRunner\Task\Status;
use Teknoo\East\CodeRunner\Task\TextResult;
use Teknoo\East\CodeRunner\Worker\Interfaces\ComposerConfiguratorInterface;
use Teknoo\East\CodeRunner\Worker\Interfaces\PHPCommanderInterface;
use Teknoo\East\CodeRunner\Worker\Interfaces\RunnerInterface;

/**
 * Class PHP7Runner
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class PHP7Runner implements ConsumerInterface, RunnerInterface
{
    /**
     * @var ProducerInterface
     */
    private $statusProducer;

    /**
     * @var ProducerInterface
     */
    private $resultProducer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $version;

    /**
     * @var ComposerConfiguratorInterface
     */
    private $composerConfigurator;

    /**
     * @var PHPCommanderInterface
     */
    private $phpCommander;

    /**
     * PHP7Runner constructor.
     *
     * @param ProducerInterface             $statusProducer
     * @param ProducerInterface             $resultProducer
     * @param LoggerInterface               $logger
     * @param string                        $version
     * @param ComposerConfiguratorInterface $composerConfigurator
     * @param PHPCommanderInterface         $phpCommander
     */
    public function __construct(
        ProducerInterface $statusProducer,
        ProducerInterface $resultProducer,
        LoggerInterface $logger,
        string $version,
        ComposerConfiguratorInterface $composerConfigurator,
        PHPCommanderInterface $phpCommander
    ) {
        $this->statusProducer = $statusProducer;
        $this->resultProducer = $resultProducer;
        $this->logger = $logger;
        $this->version = $version;
        $this->composerConfigurator = $composerConfigurator;
        $this->phpCommander = $phpCommander;
    }

    /**
     * @param AMQPMessage $message
     *
     * @return Task
     */
    private function extractTask(AMQPMessage $message): Task
    {
        return Task::jsonDeserialize(\json_decode($message->body, true));
    }

    /**
     * @param AMQPMessage $msg
     *
     * @return bool
     */
    public function execute(AMQPMessage $msg)
    {
        try {
            $this->statusProducer->publish(json_encode(new Status('Prepare')));

            $task = $this->extractTask($msg);

            $this->composerConfigurator->configure($task->getCode(), $this);
        } catch (\Throwable $e) {
            $error = $e->getMessage().PHP_EOL;
            $error .= $e->getFile().':'.$e->getLine().PHP_EOL;
            $error .= $e->getTraceAsString();

            $this->logger->critical($e);

            $result = new TextResult(
                '',
                $error,
                $this->version,
                \memory_get_usage(true),
                0
            );

            $this->resultProducer->publish(json_encode($result));
            $this->statusProducer->publish(json_encode(new Status('Failure')));

            return false;
        }

        return true;
    }

    /**
     * @param CodeInterface $code
     *
     * @return RunnerInterface
     */
    public function composerIsReady(CodeInterface $code): RunnerInterface
    {
        $this->statusProducer->publish(json_encode(new Status('Executing')));
        $this->phpCommander->execute($code, $this);

        return $this;
    }

    private function reset()
    {
        $this->composerConfigurator->reset();
        $this->phpCommander->reset();
    }

    /**
     * @param CodeInterface   $code
     * @param ResultInterface $result
     *
     * @return RunnerInterface
     */
    public function codeExecuted(CodeInterface $code, ResultInterface $result): RunnerInterface
    {
        $this->resultProducer->publish(json_encode($result));
        $this->statusProducer->publish(json_encode(new Status('Finished')));

        $this->reset();

        return $this;
    }

    /**
     * @param CodeInterface   $code
     * @param ResultInterface $result
     *
     * @return RunnerInterface
     */
    public function errorInCode(CodeInterface $code, ResultInterface $result): RunnerInterface
    {
        $this->resultProducer->publish(json_encode($result));
        $this->statusProducer->publish(json_encode(new Status('Failure')));

        $this->reset();

        return $this;
    }
}
