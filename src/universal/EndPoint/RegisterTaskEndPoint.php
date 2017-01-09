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
use Teknoo\East\CodeRunner\Manager\Interfaces\TaskManagerInterface;
use Teknoo\East\CodeRunner\Task\PHPCode;
use Teknoo\East\Foundation\Http\ClientInterface;
use Teknoo\East\FoundationBundle\Controller\EastControllerTrait;

/**
 * Class RegisterTaskEndPoint
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class RegisterTaskEndPoint
{
    use EastControllerTrait;

    /**
     * @var TaskManagerInterface
     */
    private $tasksManager;

    /**
     * RegisterTaskEndPoint constructor.
     * @param TaskManagerInterface $tasksManager
     */
    public function __construct(TaskManagerInterface $tasksManager)
    {
        $this->tasksManager = $tasksManager;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @param ClientInterface $client
     * @param string $code
     * @return self
     */
    public function __invoke(ServerRequestInterface $serverRequest, ClientInterface $client, string $code)
    {
        $task = new Task();
        $task->setCode(new PHPCode($code, []));

        $this->tasksManager->executeMe($task);

        try {
            $task->getUrl();
        } catch (\Throwable $e) {
            $client->responseFromController(new Response(501, [], json_encode(['success'=>false, 'message'=>'Task is not registered'])));

            return $this;
        }

        $client->responseFromController(
            new Response(200, [], \json_encode($task))
        );

        return $this;
    }
}
