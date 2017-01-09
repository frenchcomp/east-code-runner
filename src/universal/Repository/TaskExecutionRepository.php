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
use Teknoo\East\CodeRunner\Entity\TaskExecution;

/**
 * Class TaskExecutionRepository
 *
 * @copyright   Copyright (c) 2009-2017 Richard Déloge (richarddeloge@gmail.com)
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class TaskExecutionRepository extends EntityRepository
{
    /**
     * @var TaskExecution[]
     */
    private $tasksExecutionsList = [];

    /**
     * @param string $identifier
     *
     * @return TaskExecution|false
     */
    private function fetchTaskExecution(string $identifier)
    {
        $queryBuilder = $this->createQueryBuilder('te');
        $queryBuilder->innerJoin('te.task', 't');
        $queryBuilder->addSelect('t');
        $queryBuilder->andWhere('te.runnerIdentifier = :runnerIdentifier');
        $queryBuilder->setParameter('runnerIdentifier', $identifier);

        $query = $queryBuilder->getQuery();
        $taskExecution = $query->getOneOrNullResult();

        if (!$taskExecution instanceof TaskExecution) {
            return false;
        }

        return $taskExecution;
    }

    /**
     * @param string $identifier
     *
     * @return TaskExecution|false
     */
    public function findByRunnerIdentifier(string $identifier)
    {
        if (!isset($this->tasksExecutionsList[$identifier])) {
            $this->tasksExecutionsList[$identifier] = $this->fetchTaskExecution($identifier);
        }

        return $this->tasksExecutionsList[$identifier];
    }

    /**
     * To perform a batch update request to delete all entries of task executions.
     *
     * @param \DateTime $date
     *
     * @return TaskExecutionRepository
     */
    public function clearAll(\DateTime $date): TaskExecutionRepository
    {
        //Prepare the request update
        $queryBuilder = $this->createQueryBuilder('te');
        $queryBuilder->update();
        $queryBuilder->andWhere('te.deletedAd <> null');
        $queryBuilder->andWhere('te.deletedAt = :dateValue');
        $queryBuilder->setParameter('dateValue', $date->format('Y-m-d H:i:s'));

        //Execute it
        $query = $queryBuilder->getQuery();
        $query->execute();

        //Clean local cache
        $this->tasksExecutionsList = [];

        return $this;
    }

    /**
     * To invalidate a specific cache.
     *
     * @param string $identifier
     *
     * @return TaskExecutionRepository
     */
    public function clearExecution(string $identifier): TaskExecutionRepository
    {
        if (isset($this->tasksExecutionsList[$identifier])) {
            unset($this->tasksExecutionsList[$identifier]);
        }

        return $this;
    }
}
