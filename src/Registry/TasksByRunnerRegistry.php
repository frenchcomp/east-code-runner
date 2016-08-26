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
namespace Teknoo\East\CodeRunnerBundle\Registry;

use Teknoo\East\CodeRunnerBundle\Registry\Interfaces\TasksByRunnerRegistryInterface;
use Teknoo\East\CodeRunnerBundle\Repository\TaskExecutionRepository;
use Teknoo\East\CodeRunnerBundle\Runner\Interfaces\RunnerInterface;
use Teknoo\East\CodeRunnerBundle\Service\DatesService;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskUserInterface;

class TasksByRunnerRegistry implements TasksByRunnerRegistryInterface
{
    /**
     * @var DatesService
     */
    private $datesService;

    /**
     * @var TaskExecutionRepository
     */
    private $taskExecutionRepository;

    /**
     * TasksByRunnerRegistry constructor.
     * @param DatesService $datesService
     * @param TaskExecutionRepository $taskExecutionRepository
     */
    public function __construct(DatesService $datesService, TaskExecutionRepository $taskExecutionRepository)
    {
        $this->datesService = $datesService;
        $this->taskExecutionRepository = $taskExecutionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        if (!$offset instanceof RunnerInterface) {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        if (!$offset instanceof RunnerInterface) {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if (!$offset instanceof RunnerInterface) {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        if (!$offset instanceof RunnerInterface) {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function registerTask(TaskInterface $task): TaskUserInterface
    {
        // TODO: Implement registerTask() method.
    }

    /**
     * {@inheritdoc}
     */
    public function clearAll(): TasksByRunnerRegistryInterface
    {
        // TODO: Implement clearAll() method.
    }
}