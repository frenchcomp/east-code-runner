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
 * Class PHP7Runner.
 * Implementation of RunnerInterface, Worker of RemotePHP7Runner of CodeBundle library to execute some PHP Task into an
 * isolated and secured environment. Tasks and returns are transmitted via two dedicated AMQP exchange.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class PHP7Runner implements ConsumerInterface, RunnerInterface
{
    /**
     * @var ProducerInterface
     */
    private $returnProducer;

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
     * @var string
     */
    private $currentTaskUrl;

    /**
     * PHP7Runner constructor.
     *
     * @param ProducerInterface             $returnProducer
     * @param LoggerInterface               $logger
     * @param string                        $version
     * @param ComposerConfiguratorInterface $composerConfigurator
     * @param PHPCommanderInterface         $phpCommander
     */
    public function __construct(
        ProducerInterface $returnProducer,
        LoggerInterface $logger,
        string $version,
        ComposerConfiguratorInterface $composerConfigurator,
        PHPCommanderInterface $phpCommander
    ) {
        $this->returnProducer = $returnProducer;
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
            $this->reset();

            $task = $this->extractTask($msg);
            $this->currentTaskUrl = $task->getUrl();

            $this->returnProducer->publish(json_encode([$this->currentTaskUrl => new Status('Prepare')]));

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

            $this->returnProducer->publish(json_encode([$this->currentTaskUrl => $result]));
            $this->returnProducer->publish(json_encode([$this->currentTaskUrl => new Status('Failure')]));

            return ConsumerInterface::MSG_REJECT;
        }

        return ConsumerInterface::MSG_ACK;
    }

    /**
     * @param CodeInterface $code
     *
     * @return RunnerInterface
     */
    public function composerIsReady(CodeInterface $code): RunnerInterface
    {
        $this->returnProducer->publish(json_encode([$this->currentTaskUrl => new Status('Executing')]));
        $this->phpCommander->execute($code, $this);

        return $this;
    }

    /**
     * To reinitialize the state of this worker
     */
    private function reset()
    {
        $this->composerConfigurator->reset();
        $this->phpCommander->reset();
        $this->currentTaskUrl = null;
    }

    /**
     * @param CodeInterface   $code
     * @param ResultInterface $result
     *
     * @return RunnerInterface
     */
    public function codeExecuted(CodeInterface $code, ResultInterface $result): RunnerInterface
    {
        $this->returnProducer->publish(json_encode([$this->currentTaskUrl => $result]));
        $this->returnProducer->publish(json_encode([$this->currentTaskUrl => new Status('Finished', true)]));

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
        $this->returnProducer->publish(json_encode([$this->currentTaskUrl => $result]));
        $this->returnProducer->publish(json_encode([$this->currentTaskUrl => new Status('Failure', true)]));

        $this->reset();

        return $this;
    }
}
