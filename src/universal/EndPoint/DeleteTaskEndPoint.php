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
use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksManagerByTasksRegistryInterface;
use Teknoo\East\CodeRunner\Registry\Interfaces\TasksRegistryInterface;
use Teknoo\East\CodeRunner\Task\Interfaces\TaskInterface;
use Teknoo\East\Foundation\Http\ClientInterface;
use Teknoo\East\FoundationBundle\Controller\EastControllerTrait;

/**
 * Class DeleteTaskEndPoint.
 * End point, used by East Foundation to allow an user to remove a task into the platform and ask its execution.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class DeleteTaskEndPoint
{
    use EastControllerTrait;

    /**
     * @var TasksManagerByTasksRegistryInterface
     */
    private $tasksManagerByTasksRegistry;

    /**
     * @var TasksRegistryInterface
     */
    private $tasksRegistry;

    /**
     * DeleteTaskEndPoint constructor.
     *
     * @param TasksManagerByTasksRegistryInterface $tasksManagerByTasksRegistry
     * @param TasksRegistryInterface               $tasksRegistry
     */
    public function __construct(
        TasksManagerByTasksRegistryInterface $tasksManagerByTasksRegistry,
        TasksRegistryInterface $tasksRegistry
    ) {
        $this->tasksManagerByTasksRegistry = $tasksManagerByTasksRegistry;
        $this->tasksRegistry = $tasksRegistry;
    }

    /**
     * To allow East processor to execute this endpoint like a method.
     *
     * @param ServerRequestInterface $serverRequest
     * @param ClientInterface        $client
     * @param string                 $taskId
     *
     * @return self
     */
    public function __invoke(
        ServerRequestInterface $serverRequest,
        ClientInterface $client,
        string $taskId
    ) {
        //Retrieve the task from the task registry<
        $task = null;
        try {
            $task = $this->tasksRegistry->get($taskId);
        } catch (\DomainException $e) {
            $client->responseFromController(new Response(404, [], json_encode(['success' => false, 'message' => 'Task not found'])));

            return $this;
        }

        //Retrieve the manager from the manager registry
        $manager = null;
        if ($task instanceof TaskInterface && isset($this->tasksManagerByTasksRegistry[$task])) {
            $manager = $this->tasksManagerByTasksRegistry[$task];
        }

        if (!$manager instanceof TaskManagerInterface) {
            $client->responseFromController(new Response(404, [], json_encode(['success' => false, 'message' => 'Task not found'])));

            return $this;
        }

        //Ask manager to forget the task
        $manager->forgetMe($task);
        $client->responseFromController(new Response(200, [], json_encode(['success' => true])));

        return $this;
    }
}
