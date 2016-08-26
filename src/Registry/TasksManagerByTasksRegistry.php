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
use Teknoo\East\CodeRunnerBundle\Registry\Interfaces\TasksManagerByTasksRegistryInterface;
use Teknoo\East\CodeRunnerBundle\Repository\TaskRegistrationRepository;
use Teknoo\East\CodeRunnerBundle\Service\DatesService;
use Teknoo\East\CodeRunnerBundle\Task\Interfaces\TaskInterface;

class TasksManagerByTasksRegistry implements TasksManagerByTasksRegistryInterface
{
    /**
     * @var DatesService
     */
    private $datesService;

    /**
     * @var TaskRegistrationRepository
     */
    private $taskRegistrationRepository;

    /**
     * TasksManagerByTasksRegistry constructor.
     * @param DatesService $datesService
     * @param TaskRegistrationRepository $taskRegistrationRepository
     */
    public function __construct(DatesService $datesService, TaskRegistrationRepository $taskRegistrationRepository)
    {
        $this->datesService = $datesService;
        $this->taskRegistrationRepository = $taskRegistrationRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        if (!$offset instanceof TaskInterface) {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        if (!$offset instanceof TaskInterface) {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if (!$offset instanceof TaskInterface) {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        if (!$offset instanceof TaskInterface) {
            throw new \InvalidArgumentException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clearAll(): TasksByRunnerRegistryInterface
    {
        // TODO: Implement clearAll() method.
    }
}