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

class TaskRegistrationRepository extends EntityRepository
{
    /**
     * @var TaskRegistration[]
     */
    private $tasksRegistrationsList = [];

    /**
     * @param string $url
     *
     * @return TaskRegistration|false
     */
    private function fetchTaskRegistration(string $url)
    {
        $queryBuilder = $this->createQueryBuilder('tr');
        $queryBuilder->innerJoin('tr.task', 't');
        $queryBuilder->addSelect('t');
        $queryBuilder->andWhere('t.url = :url');
        $queryBuilder->setParameter('url', $url);

        $query = $queryBuilder->getQuery();
        $taskRegistration = $query->getOneOrNullResult();

        if (!$taskRegistration instanceof TaskRegistration) {
            return false;
        }

        return $taskRegistration;
    }

    /**
     * @param string $url
     *
     * @return TaskRegistration|false
     */
    public function findByTaskUrl(string $url)
    {
        if (!isset($this->tasksRegistrationsList[$url])) {
            $this->tasksRegistrationsList[$url] = $this->fetchTaskRegistration($url);
        }

        return $this->tasksRegistrationsList[$url];
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
     * @param string $url
     *
     * @return TaskRegistrationRepository
     */
    public function clearRegistration(string $url): TaskRegistrationRepository
    {
        if (isset($this->tasksRegistrationsList[$url])) {
            unset($this->tasksRegistrationsList[$url]);
        }

        return $this;
    }
}
