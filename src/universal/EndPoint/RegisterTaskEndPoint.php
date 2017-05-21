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

namespace Teknoo\East\CodeRunner\EndPoint;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Teknoo\East\CodeRunner\Entity\Task\Task;
use Teknoo\East\CodeRunner\Manager\Interfaces\RunnerManagerInterface;
use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Runner\Capability;
use Teknoo\East\CodeRunner\Task\PHPCode;
use Teknoo\East\Foundation\Http\ClientInterface;
use Teknoo\East\Foundation\Promise\Promise;
use Teknoo\East\FoundationBundle\EndPoint\EastEndPointTrait;

/**
 * Class RegisterTaskEndPoint.
 * End point, used by East Foundation to allow an user to register a task into the platform and ask its execution.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class RegisterTaskEndPoint
{
    use EastEndPointTrait;

    /**
     * @var TaskManagerInterface
     */
    private $tasksManager;

    /**
     * @var RunnerManagerInterface
     */
    private $runnerManager;

    /**
     * RegisterTaskEndPoint constructor.
     *
     * @param RunnerManagerInterface $runnerManager
     */
    public function __construct(RunnerManagerInterface $runnerManager)
    {
        $this->runnerManager = $runnerManager;
    }

    /**
     * @param TaskManagerInterface $tasksManager
     *
     * @return RegisterTaskEndPoint
     */
    public function registerTaskManager(TaskManagerInterface $tasksManager): RegisterTaskEndPoint
    {
        $this->tasksManager = $tasksManager;

        return $this;
    }

    /**
     * To allow East processor to execute this endpoint like a method.
     *
     * @param ServerRequestInterface $serverRequest
     * @param ClientInterface        $client
     *
     * @return self
     */
    public function __invoke(ServerRequestInterface $serverRequest, ClientInterface $client)
    {
        $codeJson = \json_decode((string) $serverRequest->getBody(), true);

        $task = new Task();
        try {
            $code = PHPCode::jsonDeserialize($codeJson);
            $task->setCode($code);
        } catch (\Throwable $e) {
            $client->responseFromController(
                new Response(501, [], json_encode(['success' => false, 'message' => $e->getMessage()]))
            );

            return $this;
        }

        $this->tasksManager->registerRunnerManager($this->runnerManager);
        $this->tasksManager->executeMe(
            $task,
            new Promise(
                function (Task $task) use ($client) {
                    $client->responseFromController(
                        new Response(200, [], \json_encode($task))
                    );
                },
                function () use ($client) {
                    $client->responseFromController(
                        new Response(501, [], json_encode(['success' => false, 'message' => 'Task is not registered']))
                    );
                }
            )
        );

        return $this;
    }
}
