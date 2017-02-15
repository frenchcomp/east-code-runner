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

namespace Teknoo\East\CodeRunner\Repository;

use Doctrine\ORM\EntityRepository;
use Teknoo\East\CodeRunner\Entity\TaskRegistration;

/**
 * Class TaskRegistrationRepository.
 * Registry to manage TaskRegistration Entity.
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class TaskRegistrationRepository extends EntityRepository
{
    /**
     * @var TaskRegistration[]
     */
    private $tasksRegistrationsList = [];

    /**
     * To return a TaskRegistration, from the task's identifier. If there are no TaskExecution found, the method returns
     * false.
     *
     * @param string $taskId
     *
     * @return TaskRegistration|false
     */
    private function fetchTaskRegistration(string $taskId)
    {
        $queryBuilder = $this->createQueryBuilder('tr');
        $queryBuilder->innerJoin('tr.task', 't');
        $queryBuilder->addSelect('t');
        $queryBuilder->andWhere('t.id = :taskId');
        $queryBuilder->andWhere('tr.deletedAt is null');
        $queryBuilder->andWhere('t.deletedAt is null');
        $queryBuilder->setParameter('taskId', $taskId);
        $queryBuilder->setMaxResults(1);

        $query = $queryBuilder->getQuery();
        $taskRegistration = $query->getOneOrNullResult();

        if (!$taskRegistration instanceof TaskRegistration) {
            return false;
        }

        return $taskRegistration;
    }

    /**
     * To get a TaskExecution from the Runner's identifier. If the TaskExecution has been already fetched,
     * the repository use it's cache.
     *
     * @param string $taskId
     *
     * @return TaskRegistration|false
     */
    public function findByTaskId(string $taskId)
    {
        if (!isset($this->tasksRegistrationsList[$taskId])) {
            $result = $this->fetchTaskRegistration($taskId);

            if ($result instanceof TaskRegistration) {
                $this->tasksRegistrationsList[$taskId] = $result;
            }

            return $result;
        }

        return $this->tasksRegistrationsList[$taskId];
    }

    /**
     * To perform a batch update request to delete all entries of task executions.
     *
     * @param \DateTime $date
     *
     * @return TaskRegistrationRepository
     */
    public function clearAll(\DateTime $date): TaskRegistrationRepository
    {
        //Prepare the request update
        $queryBuilder = $this->createQueryBuilder('tr');
        $queryBuilder->update();
        $queryBuilder->andWhere('tr.deletedAd <> null');
        $queryBuilder->andWhere('tr.deletedAt = :dateValue');
        $queryBuilder->setParameter('dateValue', $date->format('Y-m-d H:i:s'));

        //Execute it
        $query = $queryBuilder->getQuery();
        $query->execute();

        //Clean local cache
        $this->tasksRegistrationsList = [];

        return $this;
    }

    /**
     * To invalidate a specific cache.
     *
     * @param string $taskId
     *
     * @return TaskRegistrationRepository
     */
    public function clearRegistration(string $taskId): TaskRegistrationRepository
    {
        if (isset($this->tasksRegistrationsList[$taskId])) {
            unset($this->tasksRegistrationsList[$taskId]);
        }

        return $this;
    }
}
