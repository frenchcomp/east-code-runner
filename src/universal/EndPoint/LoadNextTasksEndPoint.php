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
use Teknoo\East\CodeRunner\Manager\RunnerManager\RunnerManager;
use Teknoo\East\Foundation\Http\ClientInterface;
use Teknoo\East\FoundationBundle\Controller\EastControllerTrait;

/**
 * Class LoadNextTasksEndPoint.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class LoadNextTasksEndPoint
{
    use EastControllerTrait;

    /**
     * @var RunnerManager
     */
    private $runnerManager;

    /**
     * RegisterTaskEndPoint constructor.
     *
     * @param RunnerManager $runnerManager
     */
    public function __construct(RunnerManager $runnerManager)
    {
        $this->runnerManager = $runnerManager;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @param ClientInterface        $client
     *
     * @return self
     */
    public function __invoke(ServerRequestInterface $serverRequest, ClientInterface $client)
    {
        $this->runnerManager->loadNextTasks();

        $client->responseFromController(
            new Response(200, [], \json_encode(['success'=>true]))
        );

        return $this;
    }
}
